import 'package:flutter/material.dart';

import '../../services/api_client.dart';
import '../../theme/app_colors.dart';
import '../../utils/validators.dart';
import '../../widgets/app_widgets.dart';

/// Instructor creates a class with its attendance times.
class CreateClassScreen extends StatefulWidget {
  const CreateClassScreen({super.key});

  @override
  State<CreateClassScreen> createState() => _CreateClassScreenState();
}

class _CreateClassScreenState extends State<CreateClassScreen> {
  final _formKey = GlobalKey<FormState>();
  final _name = TextEditingController();
  final _description = TextEditingController();
  final _section = TextEditingController(text: 'General');
  final _present = TextEditingController(text: '08:00');
  final _late = TextEditingController(text: '08:15');
  final _end = TextEditingController(text: '09:00');

  bool _saving = false;
  String? _error;

  @override
  void dispose() {
    for (final c in [_name, _description, _section, _present, _late, _end]) {
      c.dispose();
    }
    super.dispose();
  }

  Future<void> _pickTime(TextEditingController controller) async {
    final parts = controller.text.split(':');
    final time = await showTimePicker(
      context: context,
      initialTime: TimeOfDay(
        hour: parts.isNotEmpty ? int.tryParse(parts[0]) ?? 8 : 8,
        minute: parts.length > 1 ? int.tryParse(parts[1]) ?? 0 : 0,
      ),
    );
    if (time != null) {
      controller.text =
          '${time.hour.toString().padLeft(2, '0')}:${time.minute.toString().padLeft(2, '0')}';
    }
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() {
      _saving = true;
      _error = null;
    });
    try {
      await ApiClient.instance.post('/classes', body: {
        'name': _name.text.trim(),
        'description': _description.text.trim(),
        'section': _section.text.trim().isEmpty ? 'General' : _section.text.trim(),
        'present_time': _present.text.trim(),
        'late_time': _late.text.trim(),
        'end_time': _end.text.trim(),
      });
      if (!mounted) return;
      Navigator.of(context).pop(true);
    } on ApiException catch (e) {
      if (mounted) setState(() => _error = e.message);
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('New class')),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20),
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 480),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  AppTextField(
                    controller: _name,
                    label: 'Class name',
                    icon: Icons.class_outlined,
                    validator: (v) => requiredField(v, 'Class name'),
                  ),
                  const SizedBox(height: 14),
                  AppTextField(
                    controller: _description,
                    label: 'Description (optional)',
                    icon: Icons.description_outlined,
                  ),
                  const SizedBox(height: 14),
                  AppTextField(
                    controller: _section,
                    label: 'Section',
                    icon: Icons.groups_outlined,
                  ),
                  const SizedBox(height: 18),
                  const Text('Attendance times',
                      style: TextStyle(
                          color: AppColors.text, fontWeight: FontWeight.w700)),
                  const SizedBox(height: 10),
                  _timeField('Present before', Icons.check_circle_outline, _present),
                  const SizedBox(height: 10),
                  _timeField('Late after', Icons.access_time, _late),
                  const SizedBox(height: 10),
                  _timeField('Class ends', Icons.stop_circle_outlined, _end),
                  if (_error != null) ...[
                    const SizedBox(height: 16),
                    ErrorBanner(_error!),
                  ],
                  const SizedBox(height: 22),
                  PrimaryButton(
                      label: 'Create class',
                      loading: _saving,
                      onPressed: _saving ? null : _submit),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _timeField(String label, IconData icon, TextEditingController controller) {
    return TextFormField(
      controller: controller,
      readOnly: true,
      onTap: () => _pickTime(controller),
      style: const TextStyle(color: AppColors.text),
      validator: (v) => requiredField(v, label),
      decoration: InputDecoration(
        labelText: label,
        labelStyle: const TextStyle(color: AppColors.label),
        prefixIcon: Icon(icon, color: AppColors.muted),
        filled: true,
        fillColor: AppColors.ink700,
        border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12),
            borderSide: const BorderSide(color: AppColors.line)),
      ),
    );
  }
}
