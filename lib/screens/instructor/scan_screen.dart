import 'dart:async';

import 'package:camera/camera.dart';
import 'package:flutter/material.dart';

import '../../models/class_info.dart';
import '../../services/api_client.dart';
import '../../theme/app_colors.dart';
import '../../widgets/app_widgets.dart';

/// Instructor's phone camera: streams frames to the backend, which recognises
/// students and marks the attendance live.
class ScanScreen extends StatefulWidget {
  const ScanScreen({super.key, required this.classInfo});

  final ClassInfo classInfo;

  @override
  State<ScanScreen> createState() => _ScanScreenState();
}

class _ScanScreenState extends State<ScanScreen> {
  CameraController? _camera;
  Timer? _timer;
  bool _busy = false;
  bool _starting = false;
  bool _sessionActive = false;
  String? _error;
  List<dynamic> _faces = [];
  List<dynamic> _roster = [];

  @override
  void initState() {
    super.initState();
    _initCamera();
    _checkSession();
  }

  @override
  void dispose() {
    _timer?.cancel();
    _camera?.dispose();
    super.dispose();
  }

  Future<void> _initCamera() async {
    try {
      final cameras = await availableCameras();
      if (cameras.isEmpty) {
        if (mounted) setState(() => _error = 'No camera found on this device.');
        return;
      }
      final back = cameras.firstWhere(
        (c) => c.lensDirection == CameraLensDirection.back,
        orElse: () => cameras.first,
      );
      final controller = CameraController(
        back,
        ResolutionPreset.medium,
        enableAudio: false,
        imageFormatGroup: ImageFormatGroup.jpeg,
      );
      await controller.initialize();
      if (!mounted) {
        await controller.dispose();
        return;
      }
      setState(() => _camera = controller);
      _startStreaming();
    } catch (e) {
      if (mounted) setState(() => _error = 'Camera error: $e');
    }
  }

  Future<void> _checkSession() async {
    try {
      final data = await ApiClient.instance.get('/attendance/session');
      if (mounted) {
        setState(() => _sessionActive = data['active'] as bool? ?? false);
      }
    } on ApiException {
      // The start button remains available.
    }
  }

  void _startStreaming() {
    _timer?.cancel();
    _timer = Timer.periodic(const Duration(milliseconds: 550), (_) => _sendFrame());
  }

  Future<void> _sendFrame() async {
    final camera = _camera;
    if (_busy || !_sessionActive || camera == null ||
        !camera.value.isInitialized || !mounted) {
      return;
    }
    setState(() => _busy = true);
    try {
      final picture = await camera.takePicture();
      final bytes = await picture.readAsBytes();
      final data = await ApiClient.instance.post('/attendance/frame',
          body: {'image': ApiClient.encodeImage(bytes)});
      if (!mounted) return;
      setState(() {
        _faces = data['faces'] as List? ?? [];
        _roster = data['roster'] as List? ?? [];
        _error = null;
      });
    } on ApiException catch (e) {
      if (mounted) setState(() => _error = e.message);
    } catch (_) {
      // A frame can fail while the camera is focusing; the next one will work.
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  Future<void> _startClass() async {
    setState(() => _starting = true);
    try {
      await ApiClient.instance.post('/attendance/session/start',
          body: {'class_id': widget.classInfo.id});
      if (!mounted) return;
      setState(() => _sessionActive = true);
    } on ApiException catch (e) {
      if (mounted) setState(() => _error = e.message);
    } finally {
      if (mounted) setState(() => _starting = false);
    }
  }

  Future<void> _endClass() async {
    _timer?.cancel();
    try {
      await ApiClient.instance.post('/attendance/session/end');
      if (!mounted) return;
      setState(() {
        _sessionActive = false;
        _faces = [];
      });
      ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Class ended. Attendance saved.')));
    } on ApiException catch (e) {
      if (mounted) setState(() => _error = e.message);
      _startStreaming();
    }
  }

  Color _stateColor(String state) => switch (state) {
        'present' => AppColors.leaf,
        'blink' => AppColors.gold,
        'unknown' => AppColors.danger,
        _ => AppColors.muted,
      };

  @override
  Widget build(BuildContext context) {
    final camera = _camera;
    return Scaffold(
      appBar: AppBar(
        title: Text('Scan · ${widget.classInfo.name}'),
        actions: [
          IconButton(
            tooltip: 'Refresh roster',
            onPressed: () => ApiClient.instance.get('/attendance/session').then((data) {
              if (mounted) {
                setState(() {
                  _sessionActive = data['active'] as bool? ?? false;
                  _roster = data['roster'] as List? ?? [];
                });
              }
            }),
            icon: const Icon(Icons.refresh),
          ),
        ],
      ),
      body: SafeArea(
        child: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            if (_error != null) ...[
              ErrorBanner(_error!),
              const SizedBox(height: 12),
            ],
            if (!_sessionActive)
              InfoCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Start the class first',
                        style: TextStyle(
                            color: AppColors.text,
                            fontSize: 17,
                            fontWeight: FontWeight.w700)),
                    const SizedBox(height: 6),
                    const Text(
                      'Students are marked Present before the Late time and '
                      'Late after it. Everyone not seen when the class ends '
                      'is marked Absent.',
                      style: TextStyle(color: AppColors.muted),
                    ),
                    const SizedBox(height: 14),
                    PrimaryButton(
                      label: 'Start class',
                      loading: _starting,
                      onPressed: _starting ? null : _startClass,
                    ),
                  ],
                ),
              ),
            if (_sessionActive) ...[
              _buildPreview(camera),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: () => ApiClient.instance
                          .post('/attendance/frame/reset'),
                      icon: const Icon(Icons.restart_alt),
                      label: const Text('Reset tracking'),
                      style: OutlinedButton.styleFrom(
                          foregroundColor: AppColors.muted,
                          side: const BorderSide(color: AppColors.line)),
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: FilledButton.icon(
                      onPressed: _endClass,
                      icon: const Icon(Icons.stop_circle_outlined),
                      label: const Text('End class'),
                      style: FilledButton.styleFrom(
                          backgroundColor: AppColors.danger,
                          foregroundColor: Colors.white),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              Text('Detected (${_faces.length})',
                  style: const TextStyle(
                      color: AppColors.text, fontWeight: FontWeight.w700)),
              const SizedBox(height: 8),
              if (_faces.isEmpty)
                const Text('Point the phone at your students…',
                    style: TextStyle(color: AppColors.muted))
              else
                Wrap(
                  spacing: 8,
                  runSpacing: 8,
                  children: _faces.map((f) {
                    final face = f as Map<String, dynamic>;
                    return Chip(
                      avatar: CircleAvatar(
                          backgroundColor:
                              _stateColor(face['state'] ?? 'checking')),
                      label: Text(face['name'] ?? '?',
                          style: const TextStyle(color: AppColors.text)),
                      backgroundColor: AppColors.ink800,
                    );
                  }).toList(),
                ),
              const SizedBox(height: 16),
              Text('Live roster (${_roster.length})',
                  style: const TextStyle(
                      color: AppColors.text, fontWeight: FontWeight.w700)),
              const SizedBox(height: 8),
              ..._roster.map((r) {
                final row = r as Map<String, dynamic>;
                final status = row['status'] ?? 'absent';
                return ListTile(
                  contentPadding: EdgeInsets.zero,
                  leading: CircleAvatar(
                      backgroundColor: _stateColor(status),
                      radius: 6),
                  title: Text(row['name'] ?? '',
                      style: const TextStyle(color: AppColors.text)),
                  trailing: Text('${status.toUpperCase()} ${row['time'] ?? ''}',
                      style: TextStyle(
                          color: _stateColor(status), fontSize: 12)),
                );
              }),
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildPreview(CameraController? camera) {
    if (camera == null || !camera.value.isInitialized) {
      return Container(
        height: 260,
        decoration: BoxDecoration(
            color: AppColors.ink800,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppColors.line)),
        child: const Center(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              CircularProgressIndicator(color: AppColors.gold),
              SizedBox(height: 12),
              Text('Opening camera…', style: TextStyle(color: AppColors.muted)),
            ],
          ),
        ),
      );
    }
    return ClipRRect(
      borderRadius: BorderRadius.circular(16),
      child: AspectRatio(
        aspectRatio: camera.value.aspectRatio,
        child: LayoutBuilder(
          builder: (context, constraints) => Stack(
          fit: StackFit.expand,
          children: [
            CameraPreview(camera),
            ..._faces.map((f) {
              final face = f as Map<String, dynamic>;
              final box = (face['box'] as List?) ?? [];
              if (box.length != 4) return const SizedBox();
              final left = (box[0] as num).toDouble();
              final top = (box[1] as num).toDouble();
              final right = (box[2] as num).toDouble();
              final bottom = (box[3] as num).toDouble();
              final color = _stateColor(face['state'] ?? 'checking');
              return Positioned(
                left: left * constraints.maxWidth,
                top: top * constraints.maxHeight,
                width: (right - left) * constraints.maxWidth,
                height: (bottom - top) * constraints.maxHeight,
                child: Container(
                  decoration: BoxDecoration(
                      border: Border.all(color: color, width: 3),
                      borderRadius: BorderRadius.circular(6)),
                  alignment: Alignment.topLeft,
                  child: Container(
                    color: color,
                    padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 2),
                    child: Text(face['name'] ?? '',
                        style: const TextStyle(
                            color: Colors.white, fontSize: 11)),
                  ),
                ),
              );
            }),
          ],
        ),
      ),
    ),
  );
}
}
