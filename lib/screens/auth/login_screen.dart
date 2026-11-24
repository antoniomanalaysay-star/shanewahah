import 'package:flutter/material.dart';

import '../../services/api_client.dart';
import '../../services/session.dart';
import '../../theme/app_colors.dart';
import '../../utils/validators.dart';
import '../../widgets/app_widgets.dart';
import 'auth_hero.dart';
import 'register_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _email = TextEditingController();
  final _password = TextEditingController();

  bool _loading = false;
  String? _error;

  @override
  void dispose() {
    _email.dispose();
    _password.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      await Session.instance.login(_email.text.trim(), _password.text);
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
        title: 'A Camera-Based Classroom\nAttendance & Management System',
        description:
            'Recognize students at the door, keep a clean attendance calendar '
            'for every class, and give teachers and students their own workspace.',
        points: [
          'Face registration with 3 photos',
          'Live recognition with blink check',
          'Color-coded monthly attendance',
          'Announcements, excuses & class codes',
        ],
        footer: Text(
          'Admin demo: ThirdyAdmin / Admin123',
          style: TextStyle(color: AppColors.gold, fontSize: 13),
        ),
      ),
      child: Form(
        key: _formKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            const Text(
              'Welcome back',
              style: TextStyle(
                color: AppColors.text,
                fontSize: 28,
                fontWeight: FontWeight.w800,
              ),
            ),
            const SizedBox(height: 8),
            const Text(
              'Log in with the username or email and password you created.',
              style: TextStyle(color: AppColors.muted, height: 1.5),
            ),
            const SizedBox(height: 28),
            AppTextField(
              controller: _email,
              label: 'Username or email',
              icon: Icons.person_outline,
              textInputAction: TextInputAction.next,
              validator: (v) => requiredField(v, 'Username or email'),
            ),
            const SizedBox(height: 16),
            AppTextField(
              controller: _password,
              label: 'Password',
              icon: Icons.lock_outline,
              obscure: true,
              textInputAction: TextInputAction.done,
              onSubmitted: _submit,
              validator: (v) => requiredField(v, 'Password'),
            ),
            const SizedBox(height: 10),
            Align(
              alignment: Alignment.centerRight,
              child: TextButton(
                onPressed: () {}, // Placeholder — no action yet.
                child: const Text('Forgot password?',
                    style: TextStyle(color: AppColors.gold)),
              ),
            ),
            if (_error != null) ...[
              const SizedBox(height: 8),
              ErrorBanner(_error!),
            ],
            const SizedBox(height: 12),
            PrimaryButton(
              label: 'Log in',
              loading: _loading,
              onPressed: _submit,
            ),
            const SizedBox(height: 14),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Text('No account yet? ',
                    style: TextStyle(color: AppColors.muted)),
                TextButton(
                  onPressed: _loading
                      ? null
                      : () => Navigator.of(context).push(
                            MaterialPageRoute(
                              builder: (_) => const RegisterScreen(),
                            ),
                          ),
                  child: const Text('Create one',
                      style: TextStyle(color: AppColors.lime)),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
