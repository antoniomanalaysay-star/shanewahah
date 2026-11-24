import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';

import '../../services/api_client.dart';
import '../../services/session.dart';
import '../../theme/app_colors.dart';
import '../../widgets/app_widgets.dart';

/// Students register the 3 face photos used by the classroom camera.
class FaceRegistrationScreen extends StatefulWidget {
  const FaceRegistrationScreen({super.key});

  @override
  State<FaceRegistrationScreen> createState() => _FaceRegistrationScreenState();
}

class _FaceRegistrationScreenState extends State<FaceRegistrationScreen> {
  final List<String> _photos = [];
  bool _saving = false;
  String? _error;

  Future<void> _pick() async {
    final picker = ImagePicker();
    final index = _photos.length;
    final file = await picker.pickImage(
      source: ImageSource.gallery,
      imageQuality: 85,
      maxWidth: 1280,
      maxHeight: 1280,
    );
    if (file == null) return;
    final bytes = await file.readAsBytes();
    setState(() {
      if (index < _photos.length) {
        _photos[index] = ApiClient.encodeImage(bytes);
      } else {
        _photos.add(ApiClient.encodeImage(bytes));
      }
      _error = null;
    });
  }

  Future<void> _save() async {
    if (_photos.length != 3) {
      setState(() => _error = 'Choose exactly 3 photos.');
      return;
    }
    setState(() {
      _saving = true;
      _error = null;
    });
    try {
      await ApiClient.instance.post('/auth/photos', body: {'photos': _photos});
      await Session.instance.refresh();
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Face registration saved')));
      Navigator.of(context).pop();
    } on ApiException catch (e) {
      if (mounted) setState(() => _error = e.message);
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final hasFace = Session.instance.user?.hasFace ?? false;
    return Scaffold(
      appBar: AppBar(title: const Text('Face verification')),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20),
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 480),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                InfoCard(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text('Three clear photos of your face',
                          style: TextStyle(
                              color: AppColors.text,
                              fontSize: 17,
                              fontWeight: FontWeight.w700)),
                      const SizedBox(height: 8),
                      const Text(
                        'Face the camera, keep good lighting and avoid hats or '
                        'sunglasses. The instructor\'s phone will recognise you '
                        'during class.',
                        style: TextStyle(color: AppColors.muted, height: 1.4),
                      ),
                      if (hasFace) ...[
                        const SizedBox(height: 12),
                        const Text('You already have a registered face. '
                            'Uploading again replaces it.',
                            style: TextStyle(color: AppColors.lime, fontSize: 12)),
                      ],
                    ],
                  ),
                ),
                const SizedBox(height: 16),
                ...List.generate(3, (i) {
                  final selected = i < _photos.length;
                  return Padding(
                    padding: const EdgeInsets.only(bottom: 10),
                    child: ListTile(
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(14),
                          side: const BorderSide(color: AppColors.line)),
                      leading: CircleAvatar(
                        backgroundColor: AppColors.ink700,
                        child: Icon(
                            selected ? Icons.check : Icons.add_a_photo_outlined,
                            color: selected ? AppColors.lime : AppColors.muted),
                      ),
                      title: Text('Photo ${i + 1}${selected ? ' selected' : ''}',
                          style: const TextStyle(color: AppColors.text)),
                      subtitle: const Text('Tap to choose from your gallery',
                          style: TextStyle(color: AppColors.muted, fontSize: 12)),
                      onTap: _saving ? null : _pick,
                    ),
                  );
                }),
                if (_error != null) ...[
                  const SizedBox(height: 8),
                  ErrorBanner(_error!),
                ],
                const SizedBox(height: 16),
                PrimaryButton(
                  label: 'Save my photos',
                  loading: _saving,
                  onPressed: _saving ? null : _save,
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
