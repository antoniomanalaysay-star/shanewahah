import 'dart:async';

import 'package:flutter/material.dart';

import '../../models/class_info.dart';
import '../../services/api_client.dart';
import '../../theme/app_colors.dart';
import '../../widgets/app_widgets.dart';
import '../instructor/scan_screen.dart';

/// The original "Live camera" page, adapted to send frames from a phone.
class LiveCameraPanel extends StatefulWidget {
  const LiveCameraPanel({super.key, this.initialClass});

  /// When opened from a class page, start with that class selected.
  final ClassInfo? initialClass;

  @override
  State<LiveCameraPanel> createState() => _LiveCameraPanelState();
}

class _LiveCameraPanelState extends State<LiveCameraPanel> {
  late Future<List<ClassInfo>> _classes;
  ClassInfo? _selected;
  Map<String, dynamic> _status = {};
  Timer? _timer;
  String? _error;

  @override
  void initState() {
    super.initState();
    _selected = widget.initialClass;
    _classes = _loadClasses();
    _timer = Timer.periodic(const Duration(seconds: 2), (_) => _poll());
    _poll();
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  Future<List<ClassInfo>> _loadClasses() async {
    final data = await ApiClient.instance.get('/classes');
    final list = (data as List? ?? [])
        .map((c) => ClassInfo.fromJson(c as Map<String, dynamic>))
        .toList();
    if (list.isNotEmpty && _selected == null) _selected = list.first;
    return list;
  }

  Future<void> _poll() async {
    try {
      final data = await ApiClient.instance.get('/attendance/session');
      if (mounted) setState(() => _status = data as Map<String, dynamic>);
    } on ApiException {
      // Ignore transient polling errors.
    }
  }

  Future<void> _start() async {
    if (_selected == null) return;
    try {
      await ApiClient.instance.post('/attendance/session/start',
          body: {'class_id': _selected!.id});
      await _poll();
    } on ApiException catch (e) {
      if (mounted) setState(() => _error = e.message);
    }
  }

  Future<void> _end() async {
    try {
      await ApiClient.instance.post('/attendance/session/end');
      await _poll();
    } on ApiException catch (e) {
      if (mounted) setState(() => _error = e.message);
    }
  }

  @override
  Widget build(BuildContext context) {
    final active = _status['active'] == true;
    final roster = (_status['roster'] as List? ?? []).cast<Map<String, dynamic>>();
    return ListView(
      padding: const EdgeInsets.all(24),
      children: [
        const _Title('Live camera',
            subtitle: 'Start a class, then use the instructor’s phone to scan students.'),
        const SizedBox(height: 18),
        if (_error != null) ...[
          ErrorBanner(_error!),
          const SizedBox(height: 12),
        ],
        InfoCard(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: AppColors.alpha(
                          active ? 0xA8E063 : 0x8FAE99, active ? 0.14 : 0.08),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Text(
                      active ? 'CLASS RUNNING' : 'NO CLASS RUNNING',
                      style: TextStyle(
                        color: active ? AppColors.lime : AppColors.muted,
                        fontSize: 10,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                  ),
                  const Spacer(),
                  Text(
                    _status['model_ready'] == true
                        ? 'Model ready'
                        : 'Model loading…',
                    style: const TextStyle(
                        color: AppColors.muted, fontSize: 12),
                  ),
                ],
              ),
              const SizedBox(height: 18),
              FutureBuilder<List<ClassInfo>>(
                future: _classes,
                builder: (context, snapshot) {
                  if (!snapshot.hasData) {
                    return const LinearProgressIndicator(color: AppColors.gold);
                  }
                  final classes = snapshot.data!;
                  if (classes.isEmpty) {
                    return const Text('Create a class first.',
                        style: TextStyle(color: AppColors.muted));
                  }
                  final value = classes.any((c) => c.id == _selected?.id)
                      ? _selected!.id
                      : classes.first.id;
                  return DropdownButtonFormField<String>(
                    value: value,
                    dropdownColor: AppColors.ink800,
                    decoration: const InputDecoration(
                      labelText: 'Class',
                      labelStyle: TextStyle(color: AppColors.label),
                      border: OutlineInputBorder(),
                    ),
                    items: classes
                        .map((c) => DropdownMenuItem(
                              value: c.id,
                              child: Text(c.name,
                                  style: const TextStyle(color: AppColors.text)),
                            ))
                        .toList(),
                    onChanged: active
                        ? null
                        : (id) => setState(() =>
                            _selected = classes.firstWhere((c) => c.id == id)),
                  );
                },
              ),
              const SizedBox(height: 18),
              Wrap(
                spacing: 10,
                runSpacing: 10,
                children: [
                  FilledButton.icon(
                    onPressed: active || _selected == null ? null : _start,
                    icon: const Icon(Icons.play_arrow),
                    label: const Text('Start class & detection'),
                  ),
                  FilledButton.icon(
                    onPressed: active ? _end : null,
                    style: FilledButton.styleFrom(
                        backgroundColor: AppColors.danger,
                        foregroundColor: Colors.white),
                    icon: const Icon(Icons.stop),
                    label: const Text('End class'),
                  ),
                  FilledButton.tonalIcon(
                    onPressed: _selected == null
                        ? null
                        : () => Navigator.of(context).push(MaterialPageRoute(
                              builder: (_) =>
                                  ScanScreen(classInfo: _selected!))),
                    icon: const Icon(Icons.phone_android),
                    label: const Text('Open phone scanner'),
                  ),
                ],
              ),
            ],
          ),
        ),
        const SizedBox(height: 18),
        _Title('In the room', subtitle: '${roster.length} student(s) enrolled'),
        const SizedBox(height: 10),
        InfoCard(
          child: roster.isEmpty
              ? const Text('No students enrolled in this class.',
                  style: TextStyle(color: AppColors.muted))
              : Column(
                  children: roster.map((r) {
                    final status = r['status'] ?? 'absent';
                    return ListTile(
                      contentPadding: EdgeInsets.zero,
                      leading: CircleAvatar(
                        radius: 5,
                        backgroundColor: status == 'present'
                            ? AppColors.leaf
                            : status == 'late'
                                ? AppColors.gold
                                : AppColors.danger,
                      ),
                      title: Text(r['name'] ?? '',
                          style: const TextStyle(color: AppColors.text)),
                      trailing: Text(
                          '${status.toUpperCase()} ${r['time'] ?? ''}',
                          style: const TextStyle(
                              color: AppColors.muted, fontSize: 11)),
                    );
                  }).toList(),
                ),
        ),
      ],
    );
  }
}

class _Title extends StatelessWidget {
  const _Title(this.title, {required this.subtitle});

  final String title;
  final String subtitle;

  @override
  Widget build(BuildContext context) => Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title,
              style: const TextStyle(
                  color: AppColors.text,
                  fontSize: 20,
                  fontWeight: FontWeight.w800)),
          const SizedBox(height: 4),
          Text(subtitle, style: const TextStyle(color: AppColors.muted)),
        ],
      );
}
