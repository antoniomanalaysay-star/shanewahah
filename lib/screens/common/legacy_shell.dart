import 'package:flutter/material.dart';

import '../../config.dart';
import '../../models/app_user.dart';
import '../../services/session.dart';
import '../../theme/app_colors.dart';
import '../../widgets/app_widgets.dart';
import '../../widgets/clam_logo.dart';
import 'face_registration_screen.dart';
import 'notifications_sheet.dart';

class LegacyDestination {
  const LegacyDestination({
    required this.label,
    required this.icon,
    required this.child,
  });

  final String label;
  final IconData icon;
  final Widget child;
}

/// Desktop-style frame from the original browser app: top bar + side navigation.
/// On narrow screens the side navigation becomes a drawer.
class LegacyShell extends StatefulWidget {
  const LegacyShell({
    super.key,
    required this.user,
    required this.destinations,
    this.initialIndex = 0,
  });

  final AppUser user;
  final List<LegacyDestination> destinations;
  final int initialIndex;

  @override
  State<LegacyShell> createState() => _LegacyShellState();
}

class _LegacyShellState extends State<LegacyShell> {
  late int _index = widget.initialIndex;

  void _select(int index) {
    setState(() => _index = index);
    Navigator.of(context).maybePop(); // close the drawer on phones
  }

  @override
  Widget build(BuildContext context) {
    final wide = MediaQuery.sizeOf(context).width >= 820;
    final current = widget.destinations[_index];

    return Scaffold(
      backgroundColor: AppColors.ink900,
      appBar: wide ? null : AppBar(
        title: Row(
          children: [
            const ClamLogo(size: 28),
            const SizedBox(width: 10),
            const Text('AtClam'),
          ],
        ),
        actions: [_accountMenu(), const NotificationBell()],
      ),
      drawer: wide ? null : Drawer(
        backgroundColor: AppColors.ink800,
        child: _navColumn(compact: false),
      ),
      body: wide
          ? Row(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                SizedBox(width: 236, child: _navColumn(compact: true)),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      _topBar(current.label),
                      Expanded(child: current.child),
                    ],
                  ),
                ),
              ],
            )
          : current.child,
    );
  }

  Widget _topBar(String title) => Container(
        height: 66,
        padding: const EdgeInsets.symmetric(horizontal: 22),
        decoration: const BoxDecoration(
          color: AppColors.ink800,
          border: Border(bottom: BorderSide(color: AppColors.line)),
        ),
        child: Row(
          children: [
            const ClamLogo(size: 30),
            const SizedBox(width: 12),
            Text(
              title,
              style: const TextStyle(
                color: AppColors.text,
                fontSize: 16,
                fontWeight: FontWeight.w700,
              ),
            ),
            const Spacer(),
            _rolePill(),
            const SizedBox(width: 14),
            Text(
              widget.user.name,
              style: const TextStyle(color: AppColors.text, fontSize: 13),
            ),
            const SizedBox(width: 10),
            CircleAvatar(
              radius: 17,
              backgroundColor: AppColors.forest,
              child: Text(
                widget.user.name.isEmpty ? '?' : widget.user.name[0].toUpperCase(),
                style: const TextStyle(color: AppColors.text, fontSize: 13),
              ),
            ),
            const SizedBox(width: 8),
            const NotificationBell(),
            _accountMenu(),
            TextButton(
              onPressed: Session.instance.switchRole,
              child: const Text('Switch role',
                  style: TextStyle(color: AppColors.muted, fontSize: 12)),
            ),
          ],
        ),
      );

  Widget _rolePill() => Container(
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
        decoration: BoxDecoration(
          color: AppColors.alpha(0xA8E063, 0.12),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: AppColors.alpha(0xA8E063, 0.4)),
        ),
        child: Text(
          widget.user.role.toUpperCase(),
          style: const TextStyle(
            color: AppColors.lime,
            fontSize: 10,
            fontWeight: FontWeight.w800,
            letterSpacing: 0.8,
          ),
        ),
      );

  Widget _navColumn({required bool compact}) => Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          if (!compact)
            DrawerHeader(
              decoration: const BoxDecoration(color: AppColors.ink800),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      const ClamLogo(size: 40),
                      const SizedBox(width: 10),
                      RichText(
                        text: const TextSpan(
                          children: [
                            TextSpan(
                              text: 'AtClam\n',
                              style: TextStyle(
                                color: AppColors.text,
                                fontSize: 20,
                                fontWeight: FontWeight.w800,
                              ),
                            ),
                            TextSpan(
                              text: 'CLASSROOM ATTENDANCE',
                              style: TextStyle(
                                color: AppColors.muted,
                                fontSize: 9,
                                letterSpacing: 1,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const Spacer(),
                  Text(widget.user.name,
                      style: const TextStyle(
                          color: AppColors.text, fontWeight: FontWeight.w700)),
                  Text(widget.user.email,
                      style: const TextStyle(
                          color: AppColors.muted, fontSize: 11)),
                ],
              ),
            )
          else
            Container(
              height: 66,
              alignment: Alignment.centerLeft,
              padding: const EdgeInsets.symmetric(horizontal: 18),
              decoration: const BoxDecoration(
                border: Border(bottom: BorderSide(color: AppColors.line)),
              ),
              child: Row(
                children: [
                  const ClamLogo(size: 30),
                  const SizedBox(width: 10),
                  RichText(
                    text: const TextSpan(
                      children: [
                        TextSpan(
                          text: 'AtClam\n',
                          style: TextStyle(
                            color: AppColors.text,
                            fontSize: 17,
                            fontWeight: FontWeight.w800,
                            height: 1,
                          ),
                        ),
                        TextSpan(
                          text: 'CLASSROOM ATTENDANCE',
                          style: TextStyle(
                            color: AppColors.muted,
                            fontSize: 8,
                            letterSpacing: 1,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          Padding(
            padding: const EdgeInsets.fromLTRB(18, 22, 18, 8),
            child: Text(
              'WORKSPACE',
              style: TextStyle(
                color: AppColors.hint,
                fontSize: 10,
                fontWeight: FontWeight.w800,
                letterSpacing: 1.4,
              ),
            ),
          ),
          ...List.generate(widget.destinations.length, (i) {
            final d = widget.destinations[i];
            final active = i == _index;
            return ListTile(
              leading: Icon(d.icon,
                  size: 19, color: active ? AppColors.gold : AppColors.muted),
              title: Text(
                d.label,
                style: TextStyle(
                  color: active ? AppColors.text : AppColors.muted,
                  fontWeight: active ? FontWeight.w700 : FontWeight.w400,
                  fontSize: 13,
                ),
              ),
              selected: active,
              selectedTileColor: AppColors.alpha(0xF5C518, 0.08),
              onTap: () => _select(i),
            );
          }),
          const Spacer(),
          if (compact)
            Padding(
              padding: const EdgeInsets.all(16),
              child: Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: AppColors.ink700,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppColors.line),
                ),
                child: const Row(
                  children: [
                    Icon(Icons.circle, size: 8, color: AppColors.leaf),
                    SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        'Attendance ready\nCamera and calendars are active.',
                        style: TextStyle(
                            color: AppColors.muted, fontSize: 10, height: 1.4),
                      ),
                    ),
                  ],
                ),
              ),
            ),
        ],
      );

  Widget _accountMenu() => PopupMenuButton<String>(
        icon: const Icon(Icons.account_circle_outlined,
            color: AppColors.muted, size: 22),
        onSelected: (value) {
          if (value == 'photos') {
            Navigator.of(context).push(MaterialPageRoute(
                builder: (_) => const FaceRegistrationScreen()));
          } else if (value == 'server') {
            _showServerDialog(context);
          } else if (value == 'role') {
            Session.instance.switchRole();
          }
        },
        itemBuilder: (_) => [
          if (widget.user.isStudent)
            const PopupMenuItem(value: 'photos', child: Text('My face photos')),
          const PopupMenuItem(value: 'server', child: Text('Server address')),
          const PopupMenuItem(value: 'role', child: Text('Switch role')),
        ],
      );
}

Future<void> _showServerDialog(BuildContext context) async {
  final controller = TextEditingController(text: AppConfig.apiBaseUrl);
  final saved = await showDialog<bool>(
    context: context,
    builder: (ctx) => AlertDialog(
      backgroundColor: AppColors.ink800,
      title: const Text('Server address',
          style: TextStyle(color: AppColors.text)),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          const Text(
            'For a phone, use your computer’s LAN address, e.g. '
            'http://192.168.1.10:8000',
            style: TextStyle(color: AppColors.muted),
          ),
          const SizedBox(height: 12),
          AppTextField(
              controller: controller,
              label: 'Backend URL',
              icon: Icons.dns_outlined),
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
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
          content: Text('Server address saved. Log in again to refresh.')));
    }
  }
}
