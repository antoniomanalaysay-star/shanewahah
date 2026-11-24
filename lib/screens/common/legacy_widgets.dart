import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

import '../../models/class_info.dart';
import '../../theme/app_colors.dart';

/// Page heading used by every old-style workspace screen.
class LegacyPageTitle extends StatelessWidget {
  const LegacyPageTitle(this.title, {required this.subtitle, this.actions = const []});

  final String title;
  final String subtitle;
  final List<Widget> actions;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(title,
            style: const TextStyle(
                color: AppColors.text, fontSize: 26, fontWeight: FontWeight.w800)),
        const SizedBox(height: 6),
        Text(subtitle, style: const TextStyle(color: AppColors.muted)),
        if (actions.isNotEmpty) ...[
          const SizedBox(height: 14),
          Wrap(spacing: 8, runSpacing: 8, children: actions),
        ],
      ],
    );
  }
}

/// The big "card" sections from the original browser UI.
class LegacySectionCard extends StatelessWidget {
  const LegacySectionCard({
    super.key,
    required this.title,
    this.action,
    required this.children,
    this.padding = const EdgeInsets.fromLTRB(18, 16, 18, 18),
  });

  final String title;
  final Widget? action;
  final List<Widget> children;
  final EdgeInsetsGeometry padding;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: padding,
      decoration: BoxDecoration(
        color: AppColors.ink700,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.line),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Row(
            children: [
              Text(title,
                  style: const TextStyle(
                      color: AppColors.text,
                      fontSize: 18,
                      fontWeight: FontWeight.w700)),
              const Spacer(),
              if (action != null) action!,
            ],
          ),
          const SizedBox(height: 14),
          const Divider(color: AppColors.line, height: 1),
          const SizedBox(height: 16),
          ...children,
        ],
      ),
    );
  }
}

/// A class tile with the banner / code / actions layout of the old UI.
class LegacyClassCard extends StatelessWidget {
  const LegacyClassCard({
    super.key,
    required this.info,
    required this.onOpen,
    this.subtitle,
    this.onEdit,
    this.onDelete,
  });

  final ClassInfo info;
  final String? subtitle;
  final VoidCallback onOpen;
  final VoidCallback? onEdit;
  final VoidCallback? onDelete;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      constraints: const BoxConstraints(minWidth: 280, maxWidth: 440),
      decoration: BoxDecoration(
        color: AppColors.ink800,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.line),
      ),
      clipBehavior: Clip.antiAlias,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
            color: AppColors.ink800,
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(
                  child: Text(info.name,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(
                          color: AppColors.text,
                          fontSize: 17,
                          fontWeight: FontWeight.w800)),
                ),
                const SizedBox(width: 8),
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                  decoration: BoxDecoration(
                    color: AppColors.alpha(0xA8E063, 0.12),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(info.section.isEmpty ? 'General' : info.section,
                      style: const TextStyle(
                          color: AppColors.lime, fontSize: 11)),
                ),
              ],
            ),
          ),
          Container(
            height: 1,
            color: AppColors.line,
          ),
          Padding(
            padding: const EdgeInsets.all(14),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                Text(
                  info.description.isEmpty ? 'No description' : info.description,
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(color: AppColors.muted, height: 1.4),
                ),
                const SizedBox(height: 10),
                Text(
                  '${info.studentCount} students · ${subtitle ?? info.instructorName}',
                  style: const TextStyle(color: AppColors.muted, fontSize: 12),
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 10, vertical: 6),
                      decoration: BoxDecoration(
                        color: AppColors.ink900,
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: AppColors.line),
                      ),
                      child: Text(info.code,
                          style: const TextStyle(
                              color: AppColors.gold,
                              fontFamily: 'monospace',
                              fontSize: 14,
                              fontWeight: FontWeight.w700)),
                    ),
                    const SizedBox(width: 8),
                    TextButton.icon(
                      onPressed: () async {
                        await Clipboard.setData(ClipboardData(text: info.code));
                        if (!context.mounted) return;
                        ScaffoldMessenger.of(context).showSnackBar(SnackBar(
                            content: Text('Class code copied: ${info.code}')));
                      },
                      icon: const Icon(Icons.copy_all_outlined, size: 16),
                      label: const Text('Copy'),
                      style: TextButton.styleFrom(
                          foregroundColor: AppColors.lime,
                          padding: const EdgeInsets.symmetric(horizontal: 8)),
                    ),
                  ],
                ),
                const SizedBox(height: 10),
                Wrap(
                  spacing: 8,
                  runSpacing: 8,
                  children: [
                    FilledButton(
                      onPressed: onOpen,
                      style: FilledButton.styleFrom(
                          backgroundColor: AppColors.gold,
                          foregroundColor: AppColors.ink900,
                          padding: const EdgeInsets.symmetric(horizontal: 14),
                          minimumSize: const Size(0, 34),
                          textStyle: const TextStyle(fontSize: 12)),
                      child: const Text('Open class'),
                    ),
                    if (onEdit != null)
                      OutlinedButton(
                        onPressed: onEdit,
                        style: OutlinedButton.styleFrom(
                            foregroundColor: AppColors.text,
                            side: const BorderSide(color: AppColors.line),
                            minimumSize: const Size(0, 34),
                            textStyle: const TextStyle(fontSize: 12)),
                        child: const Text('Edit'),
                      ),
                    if (onDelete != null)
                      OutlinedButton(
                        onPressed: onDelete,
                        style: OutlinedButton.styleFrom(
                            foregroundColor: AppColors.danger,
                            side: const BorderSide(color: AppColors.danger),
                            minimumSize: const Size(0, 34),
                            textStyle: const TextStyle(fontSize: 12)),
                        child: const Text('Delete'),
                      ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

/// Read-only time field that opens Flutter's time picker.
class LegacyTimeField extends StatelessWidget {
  const LegacyTimeField({
    super.key,
    required this.controller,
    required this.label,
    this.icon = Icons.access_time,
  });

  final TextEditingController controller;
  final String label;
  final IconData icon;

  static Future<void> pick(
      BuildContext context, TextEditingController controller) async {
    final parts = controller.text.split(':');
    final picked = await showTimePicker(
      context: context,
      initialTime: TimeOfDay(
        hour: parts.isNotEmpty ? int.tryParse(parts[0]) ?? 8 : 8,
        minute: parts.length > 1 ? int.tryParse(parts[1]) ?? 0 : 0,
      ),
    );
    if (picked != null) {
      controller.text = '${picked.hour.toString().padLeft(2, '0')}:'
          '${picked.minute.toString().padLeft(2, '0')}';
    }
  }

  @override
  Widget build(BuildContext context) {
    return TextFormField(
      controller: controller,
      readOnly: true,
      onTap: () => pick(context, controller),
      style: const TextStyle(color: AppColors.text),
      validator: (v) => (v == null || v.trim().isEmpty) ? '$label is required' : null,
      decoration: InputDecoration(
        labelText: label,
        labelStyle: const TextStyle(color: AppColors.label),
        prefixIcon: Icon(icon, color: AppColors.muted),
        filled: true,
        fillColor: AppColors.ink900,
        border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(10),
            borderSide: const BorderSide(color: AppColors.line)),
      ),
    );
  }
}

/// Small gold "pill" button used throughout the old UI.
class LegacyButton extends StatelessWidget {
  const LegacyButton({
    super.key,
    required this.label,
    required this.onPressed,
    this.icon = Icons.add,
    this.danger = false,
    this.busy = false,
  });

  final String label;
  final VoidCallback? onPressed;
  final IconData icon;
  final bool danger;
  final bool busy;

  @override
  Widget build(BuildContext context) {
    final color = danger ? AppColors.danger : AppColors.gold;
    return FilledButton.icon(
      onPressed: busy ? null : onPressed,
      icon: busy
          ? const SizedBox(
              width: 14,
              height: 14,
              child: CircularProgressIndicator(strokeWidth: 2, color: AppColors.ink900),
            )
          : Icon(icon, size: 16),
      label: Text(label),
      style: FilledButton.styleFrom(
        backgroundColor: color,
        foregroundColor: danger ? Colors.white : AppColors.ink900,
        minimumSize: const Size(0, 36),
        padding: const EdgeInsets.symmetric(horizontal: 14),
        textStyle: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700),
      ),
    );
  }
}
