import 'dart:typed_data';

import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';

import '../../services/api_client.dart';
import '../../services/session.dart';
import '../../theme/app_colors.dart';
import '../../utils/validators.dart';
import '../../widgets/app_widgets.dart';
import 'auth_hero.dart';

/// Old-style signup: choose Student or Instructor, then continue.
/// Students add their 3 face photos here; instructors skip that step.
class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _name = TextEditingController();
  final _username = TextEditingController();
  final _email = TextEditingController();
  final _password = TextEditingController();
  final _confirm = TextEditingController();
  final List<Uint8List?> _photos = [null, null, null];

  String _role = 'student';
  bool _loading = false;
  String? _error;

  @override
  void dispose() {
    for (final c in [_name, _username, _email, _password, _confirm]) {
      c.dispose();
    }
    super.dispose();
  }

  Future<void> _pickPhoto(int index) async {
    final picker = ImagePicker();
    final picked = await picker.pickImage(
      source: ImageSource.gallery,
      imageQuality: 88,
      maxWidth: 1200,
    );
    if (picked == null) return;
    final bytes = await picked.readAsBytes();
    if (mounted) setState(() => _photos[index] = bytes);
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_role == 'student' && _photos.any((p) => p == null)) {
      setState(() => _error = 'Please choose 3 face photos.');
      return;
    }
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      await Session.instance.register(
        name: _name.text.trim(),
        username: _username.text.trim(),
        email: _email.text.trim(),
        password: _password.text,
        role: _role,
      );
      if (_role == 'student') {
        await ApiClient.instance.post('/auth/photos', body: {
          'photos': _photos
              .whereType<Uint8List>()
              .map((b) => ApiClient.encodeImage(b))
              .toList(),
        });
        await Session.instance.refresh();
      }
      if (mounted) Navigator.of(context).popUntil((r) => r.isFirst);
    } on ApiException catch (e) {
      if (mounted) setState(() => _error = e.message);
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return AuthLayout(
      hero: const AuthHero(
        title: 'Join the classroom\nwith your own workspace',
        description:
            'Students get a personal attendance calendar. Instructors create '
            'classes, share codes and use the live camera roster.',
        points: [
          'Pick Student or Instructor',
          'Students add 3 face photos',
          'Instructors skip face setup',
          'Land on your own dashboard',
        ],
        footer: Text(
          'Already registered? Log in from the previous screen.',
          style: TextStyle(color: AppColors.muted, fontSize: 13),
        ),
      ),
      child: Form(
        key: _formKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            const Text(
              'Create your account',
              style: TextStyle(
                color: AppColors.text,
                fontSize: 28,
                fontWeight: FontWeight.w800,
              ),
            ),
            const SizedBox(height: 8),
            const Text(
              'Tell us who you are. You’ll go straight to your workspace.',
              style: TextStyle(color: AppColors.muted, height: 1.5),
            ),
            const SizedBox(height: 22),
            _rolePicker(),
            const SizedBox(height: 20),
            AppTextField(
              controller: _name,
              label: 'Full name',
              icon: Icons.person_outline,
              textInputAction: TextInputAction.next,
              validator: (v) => requiredField(v, 'Full name'),
            ),
            const SizedBox(height: 14),
            AppTextField(
              controller: _username,
              label: 'Username',
              icon: Icons.alternate_email,
              textInputAction: TextInputAction.next,
              validator: (v) {
                final value = (v ?? '').trim();
                if (value.isEmpty) return 'Username is required';
                if (value.contains(RegExp(r'\s'))) {
                  return 'Username cannot contain spaces';
                }
                return null;
              },
            ),
            const SizedBox(height: 14),
            AppTextField(
              controller: _email,
              label: 'Email',
              icon: Icons.mail_outline,
              keyboardType: TextInputType.emailAddress,
              textInputAction: TextInputAction.next,
              validator: emailField,
            ),
            const SizedBox(height: 14),
            AppTextField(
              controller: _password,
              label: 'Password',
              icon: Icons.lock_outline,
              obscure: true,
              textInputAction: TextInputAction.next,
              validator: passwordField,
            ),
            const SizedBox(height: 14),
            AppTextField(
              controller: _confirm,
              label: 'Confirm password',
              icon: Icons.lock_outline,
              obscure: true,
              textInputAction: TextInputAction.done,
              onSubmitted: _submit,
              validator: (v) =>
                  v != _password.text ? 'Passwords do not match' : null,
            ),
            const SizedBox(height: 18),
            if (_role == 'student') _photoSlots() else _instructorNote(),
            if (_error != null) ...[
              const SizedBox(height: 16),
              ErrorBanner(_error!),
            ],
            const SizedBox(height: 22),
            PrimaryButton(
              label: 'Sign up & continue',
              loading: _loading,
              onPressed: _submit,
            ),
            const SizedBox(height: 14),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Text('Already have an account? ',
                    style: TextStyle(color: AppColors.muted)),
                TextButton(
                  onPressed:
                      _loading ? null : () => Navigator.of(context).pop(),
                  child: const Text('Log in',
                      style: TextStyle(color: AppColors.lime)),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _rolePicker() => Row(
        children: [
          Expanded(
            child: _roleButton(
              'student',
              Icons.school_outlined,
              'Student',
              'View my attendance',
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: _roleButton(
              'instructor',
              Icons.person_outline,
              'Instructor',
              'Create & manage classes',
            ),
          ),
        ],
      );

  Widget _roleButton(String value, IconData icon, String label, String sub) {
    final selected = _role == value;
    return GestureDetector(
      onTap: () => setState(() => _role = value),
      child: Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: selected ? AppColors.ink700 : AppColors.ink800,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(
            color: selected ? AppColors.gold : AppColors.line,
            width: selected ? 1.6 : 1,
          ),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(icon,
                    size: 20,
                    color: selected ? AppColors.gold : AppColors.muted),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(label,
                      style: TextStyle(
                        color: selected ? AppColors.text : AppColors.muted,
                        fontWeight: FontWeight.w700,
                      )),
                ),
                if (selected)
                  const Icon(Icons.check_circle,
                      size: 18, color: AppColors.gold),
              ],
            ),
            const SizedBox(height: 6),
            Text(sub,
                style: const TextStyle(
                    color: AppColors.muted, fontSize: 11, height: 1.3)),
          ],
        ),
      ),
    );
  }

  Widget _photoSlots() => Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Registration photos (3 required)',
              style: TextStyle(
                  color: AppColors.text, fontWeight: FontWeight.w700)),
          const SizedBox(height: 4),
          const Text(
            'Front, left and right views let the camera recognize you.',
            style: TextStyle(color: AppColors.muted, fontSize: 12),
          ),
          const SizedBox(height: 10),
          Row(
            children: List.generate(3, (i) {
              final bytes = _photos[i];
              return Expanded(
                child: GestureDetector(
                  onTap: () => _pickPhoto(i),
                  child: Container(
                    height: 92,
                    margin: EdgeInsets.only(right: i == 2 ? 0 : 8),
                    decoration: BoxDecoration(
                      color: AppColors.ink700,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(
                        color: bytes == null ? AppColors.line : AppColors.lime,
                      ),
                    ),
                    child: bytes == null
                        ? Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              const Icon(Icons.add_a_photo_outlined,
                                  color: AppColors.muted, size: 24),
                              const SizedBox(height: 6),
                              Text(
                                ['Front', 'Left', 'Right'][i],
                                style: const TextStyle(
                                    color: AppColors.muted, fontSize: 11),
                              ),
                            ],
                          )
                        : ClipRRect(
                            borderRadius: BorderRadius.circular(11),
                            child: Image.memory(bytes, fit: BoxFit.cover),
                          ),
                  ),
                ),
              );
            }),
          ),
        ],
      );

  Widget _instructorNote() => Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: AppColors.ink700,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: AppColors.line),
        ),
        child: const Row(
          children: [
            Icon(Icons.info_outline, color: AppColors.lime, size: 20),
            SizedBox(width: 10),
            Expanded(
              child: Text(
                'Instructors don’t need face photos. You can create classes '
                'and take attendance right away.',
                style: TextStyle(color: AppColors.muted, height: 1.4),
              ),
            ),
          ],
        ),
      );
}
