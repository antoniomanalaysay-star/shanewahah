import 'package:flutter/material.dart';

import '../models/app_user.dart';
import '../services/api_client.dart';
import '../services/session.dart';
import '../theme/app_colors.dart';
import '../widgets/clam_logo.dart';
import 'admin/admin_dashboard.dart';
import 'instructor/instructor_dashboard.dart';
import 'student/student_dashboard.dart';

/// Entry point of the no-login build.
///
/// Loads the demo identities, reopens the last used workspace, or shows a
/// simple role picker. There is no login or signup screen anywhere in the app.
class HomeGate extends StatefulWidget {
  const HomeGate({super.key});

  @override
  State<HomeGate> createState() => _HomeGateState();
}

class _HomeGateState extends State<HomeGate> {
  late Future<void> _init = Session.instance.loadDemoUsers();

  void _retry() => setState(() => _init = Session.instance.loadDemoUsers());

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<void>(
      future: _init,
      builder: (context, snapshot) {
        if (snapshot.connectionState != ConnectionState.done) {
          return const _Splash(message: 'Loading workspaces…');
        }
        if (snapshot.hasError) {
          return _LoadError(
            message: snapshot.error is ApiException
                ? (snapshot.error as ApiException).message
                : 'Could not load the demo workspaces.',
            onRetry: _retry,
          );
        }
        return ListenableBuilder(
          listenable: Session.instance,
          builder: (context, _) {
            final user = Session.instance.user;
            if (user == null) return const RolePicker();

            switch (user.role) {
              case 'admin':
                return const AdminDashboard();
              case 'instructor':
                return const InstructorDashboard();
              default:
                return const StudentDashboard();
            }
          },
        );
      },
    );
  }
}

/// Pick which dashboard to open. This replaces the old login screen.
class RolePicker extends StatelessWidget {
  const RolePicker({super.key});

  static const Map<String, (IconData, String, String)> _meta = {
    'student': (
      Icons.school_outlined,
      'Join a class with its code, post excuse letters and check your attendance.',
      'Open student workspace',
    ),
    'instructor': (
      Icons.front_hand_outlined,
      'Create classes, post announcements, run the live camera and mark attendance.',
      'Open instructor workspace',
    ),
    'admin': (
      Icons.admin_panel_settings_outlined,
      'Manage every student, instructor and class from one place.',
      'Open admin workspace',
    ),
  };

  @override
  Widget build(BuildContext context) {
    final users = Session.instance.demoUsers;
    return Scaffold(
      backgroundColor: AppColors.ink900,
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(28),
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 1060),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  const ClamLogo(size: 84),
                  const SizedBox(height: 18),
                  const Text(
                    'AT CLAM',
                    style: TextStyle(
                      color: AppColors.gold,
                      fontSize: 28,
                      fontWeight: FontWeight.w900,
                      letterSpacing: 3,
                    ),
                  ),
                  const SizedBox(height: 8),
                  const Text(
                    'Choose a workspace',
                    style: TextStyle(
                      color: AppColors.text,
                      fontSize: 18,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  const SizedBox(height: 6),
                  const Text(
                    'Demo mode — no login or signup needed.',
                    style: TextStyle(color: AppColors.muted, fontSize: 13),
                  ),
                  const SizedBox(height: 30),
                  Wrap(
                    alignment: WrapAlignment.center,
                    spacing: 18,
                    runSpacing: 18,
                    children: users.map(_card).toList(),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _card(AppUser user) {
    final meta = _meta[user.role] ?? _meta['student']!;
    return SizedBox(
      width: 312,
      child: Card(
        color: AppColors.ink800,
        surfaceTintColor: Colors.transparent,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
          side: const BorderSide(color: AppColors.line),
        ),
        child: InkWell(
          borderRadius: BorderRadius.circular(16),
          onTap: () => Session.instance.enterDemo(user),
          child: Padding(
            padding: const EdgeInsets.all(22),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Icon(meta.$1, size: 34, color: AppColors.gold),
                const SizedBox(height: 16),
                Text(
                  user.role == 'admin'
                      ? 'Administrator'
                      : user.role[0].toUpperCase() + user.role.substring(1),
                  style: const TextStyle(
                    color: AppColors.text,
                    fontSize: 19,
                    fontWeight: FontWeight.w800,
                  ),
                ),
                const SizedBox(height: 6),
                SizedBox(
                  height: 62,
                  child: Text(
                    meta.$2,
                    style: const TextStyle(
                      color: AppColors.muted, fontSize: 13, height: 1.45),
                  ),
                ),
                const Divider(color: AppColors.line),
                Text(
                  user.name,
                  style: const TextStyle(
                    color: AppColors.lime, fontSize: 12, fontWeight: FontWeight.w700),
                ),
                Text(
                  user.email,
                  style: const TextStyle(color: AppColors.hint, fontSize: 11),
                ),
                const SizedBox(height: 16),
                Row(
                  children: [
                    Text(
                      meta.$3,
                      style: const TextStyle(
                        color: AppColors.gold,
                        fontSize: 12,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    const Spacer(),
                    const Icon(Icons.arrow_forward_rounded,
                        size: 18, color: AppColors.gold),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _Splash extends StatelessWidget {
  const _Splash({this.message = 'Loading…'});

  final String message;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.ink900,
      body: Center(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const ClamLogo(size: 96),
            const SizedBox(height: 24),
            const CircularProgressIndicator(color: AppColors.gold),
            const SizedBox(height: 16),
            Text(message,
                style: const TextStyle(color: AppColors.muted, fontSize: 13)),
          ],
        ),
      ),
    );
  }
}

class _LoadError extends StatelessWidget {
  const _LoadError({required this.message, required this.onRetry});

  final String message;
  final VoidCallback onRetry;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.ink900,
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(28),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const ClamLogo(size: 80),
              const SizedBox(height: 20),
              const Text('Can’t reach the demo server',
                  style: TextStyle(
                      color: AppColors.text,
                      fontSize: 18,
                      fontWeight: FontWeight.w700)),
              const SizedBox(height: 10),
              Text(message,
                  textAlign: TextAlign.center,
                  style: const TextStyle(color: AppColors.muted, fontSize: 13)),
              const SizedBox(height: 20),
              FilledButton.icon(
                onPressed: onRetry,
                icon: const Icon(Icons.refresh),
                label: const Text('Try again'),
                style: FilledButton.styleFrom(
                    backgroundColor: AppColors.gold,
                    foregroundColor: AppColors.ink900),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
