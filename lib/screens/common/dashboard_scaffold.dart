import 'package:flutter/material.dart';

import '../../config.dart';
import '../../models/app_user.dart';
import '../../services/session.dart';
import '../../theme/app_colors.dart';
import '../../widgets/app_widgets.dart';
import '../../widgets/clam_logo.dart';
import 'change_password_screen.dart';
import 'face_registration_screen.dart';
import 'notifications_sheet.dart';

/// Shared frame for every dashboard: app bar with logo and an account menu.
class DashboardScaffold extends StatelessWidget {
  const DashboardScaffold({
    super.key,
    required this.title,
    required this.body,
    this.bottom,
  });

  final String title;
  final Widget body;
  final PreferredSizeWidget? bottom;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        titleSpacing: 12,
        title: Row(
          children: [
            const ClamLogo(size: 30),
            const SizedBox(width: 10),
            Text(title),
          ],
        ),
        bottom: bottom,
        actions: [
          const NotificationBell(),
          PopupMenuButton<String>(
            icon: const Icon(Icons.account_circle_outlined),
            onSelected: (value) {
              if (value == 'password') {
                Navigator.of(context).push(
                  MaterialPageRoute(
                    builder: (_) => const ChangePasswordScreen(),
                  ),
                );
              } else if (value == 'photos') {
                Navigator.of(context).push(
                  MaterialPageRoute(
                    builder: (_) => const FaceRegistrationScreen(),
                  ),
                );
              } else if (value == 'server') {
                _showServerDialog(context);
              } else if (value == 'logout') {
                Session.instance.logout();
              }
            },
            itemBuilder: (_) => [
              const PopupMenuItem(value: 'password', child: Text('Change password')),
              if (Session.instance.user?.isStudent ?? false)
                const PopupMenuItem(value: 'photos', child: Text('My face photos')),
              const PopupMenuItem(value: 'server', child: Text('Server address')),
              const PopupMenuItem(value: 'logout', child: Text('Log out')),
            ],
          ),
        ],
      ),
      body: body,
    );
  }
}

/// Lets a real phone point at the computer running the Python backend.
Future<void> _showServerDialog(BuildContext context) async {
  final controller = TextEditingController(text: AppConfig.apiBaseUrl);
  final saved = await showDialog<bool>(
    context: context,
    builder: (ctx) => AlertDialog(
      title: const Text('Server address'),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          const Text(
            'Use your computer\'s LAN address when running the app on a phone, '
            'for example http://192.168.1.10:8000',
            style: TextStyle(color: AppColors.muted),
          ),
          const SizedBox(height: 12),
          AppTextField(
            controller: controller,
            label: 'Backend URL',
            icon: Icons.dns_outlined,
          ),
        ],
      ),
      actions: [
        TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text('Cancel')),
        FilledButton(
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('Save')),
      ],
    ),
  );
  if (saved == true) {
    await AppConfig.setBaseUrl(controller.text);
    if (context.mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Server address saved. Log in again to refresh.')));
    }
  }
}

/// "Welcome, name" card at the top of a dashboard.
class WelcomeCard extends StatelessWidget {
  const WelcomeCard({super.key, required this.user, required this.roleLabel});

  final AppUser user;
  final String roleLabel;

  @override
  Widget build(BuildContext context) {
    final details = <String>[
      roleLabel,
      if (user.schoolId != null) 'ID ${user.schoolId}',
      user.email,
    ];
    return InfoCard(
      child: Row(
        children: [
          CircleAvatar(
            radius: 26,
            backgroundColor: AppColors.forest,
            child: Text(
              user.name.isEmpty ? '?' : user.name[0].toUpperCase(),
              style: const TextStyle(
                color: AppColors.text,
                fontSize: 22,
                fontWeight: FontWeight.w700,
              ),
            ),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Welcome, ${user.name}',
                  style: const TextStyle(
                    color: AppColors.text,
                    fontSize: 18,
                    fontWeight: FontWeight.w700,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  details.join('  •  '),
                  style: const TextStyle(color: AppColors.muted, fontSize: 13),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

/// A feature card for something that is built in a later step.
class ComingSoonCard extends StatelessWidget {
  const ComingSoonCard({
    super.key,
    required this.icon,
    required this.title,
    required this.description,
    this.extra,
  });

  final IconData icon;
  final String title;
  final String description;
  final Widget? extra;

  @override
  Widget build(BuildContext context) {
    return InfoCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(icon, color: AppColors.lime),
              const SizedBox(width: 10),
              Expanded(
                child: Text(
                  title,
                  style: const TextStyle(
                    color: AppColors.text,
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color: AppColors.alpha(0xF5C518, 0.15),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: const Text(
                  'Coming next',
                  style: TextStyle(color: AppColors.gold, fontSize: 11),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Text(
            description,
            style: const TextStyle(color: AppColors.muted, height: 1.4),
          ),
          if (extra != null) ...[
            const SizedBox(height: 12),
            extra!,
          ],
        ],
      ),
    );
  }
}
