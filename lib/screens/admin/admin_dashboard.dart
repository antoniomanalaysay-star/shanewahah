import 'package:flutter/material.dart';

import '../../services/api_client.dart';
import '../../services/session.dart';
import '../../theme/app_colors.dart';
import '../../widgets/app_widgets.dart';
import '../common/legacy_shell.dart';
import '../common/legacy_widgets.dart';
import '../common/live_camera_panel.dart';
import '../common/profile_screen.dart';

class _AdminData {
  _AdminData(this.students, this.instructors, this.classes);

  final List<Map<String, dynamic>> students;
  final List<Map<String, dynamic>> instructors;
  final List<Map<String, dynamic>> classes;
}

/// Administration page from the old system: stats, three tabs and a table
/// with add / edit / delete for both people and classes.
class AdminDashboard extends StatefulWidget {
  const AdminDashboard({super.key});

  @override
  State<AdminDashboard> createState() => _AdminDashboardState();
}

class _AdminDashboardState extends State<AdminDashboard> {
  String _tab = 'student';
  late Future<_AdminData> _data;

  @override
  void initState() {
    super.initState();
    _data = _load();
  }

  Future<_AdminData> _load() async {
    final results = await Future.wait([
      ApiClient.instance.get('/admin/users', query: {'role': 'student'}),
      ApiClient.instance.get('/admin/users', query: {'role': 'instructor'}),
      ApiClient.instance.get('/admin/classes'),
    ]);
    List<Map<String, dynamic>> rows(dynamic value) =>
        (value as List? ?? []).cast<Map<String, dynamic>>();
    return _AdminData(rows(results[0]), rows(results[1]), rows(results[2]));
  }

  void _refresh() => setState(() {
        _data = _load();
      });

  void _toast(String message) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(message)));
  }

  Future<void> _delete(String kind, Map<String, dynamic> row) async {
    final name = row['full_name'] ?? row['name'] ?? 'this record';
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: AppColors.ink800,
        title: const Text('Delete record?', style: TextStyle(color: AppColors.text)),
        content: Text('Delete ${kind == 'classes' ? 'class' : 'account'} $name?',
            style: const TextStyle(color: AppColors.muted)),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(ctx, false),
              child: const Text('Cancel')),
          FilledButton(
              onPressed: () => Navigator.pop(ctx, true),
              style: FilledButton.styleFrom(backgroundColor: AppColors.danger),
              child: const Text('Delete')),
        ],
      ),
    );
    if (confirm != true) return;
    try {
      await ApiClient.instance.delete('/admin/$kind/${row['id']}');
      _refresh();
      _toast('Deleted.');
    } on ApiException catch (e) {
      _toast(e.message);
    }
  }

  Future<void> _openEditor(_AdminData data, {Map<String, dynamic>? row}) async {
    final isClass = _tab == 'classes';
    final saved = await showDialog<bool>(
      context: context,
      builder: (_) => _AdminEditor(
        isClass: isClass,
        row: row,
        tab: _tab,
        instructors: data.instructors,
      ),
    );
    if (saved == true) {
      _refresh();
      _toast('Saved.');
    }
  }

  @override
  Widget build(BuildContext context) {
    return LegacyShell(
      user: Session.instance.user!,
      destinations: [
        LegacyDestination(
            label: 'People & classes',
            icon: Icons.admin_panel_settings_outlined,
            child: _workspace()),
        const LegacyDestination(
            label: 'Live camera',
            icon: Icons.videocam_outlined,
            child: LiveCameraPanel()),
        const LegacyDestination(
            label: 'My profile',
            icon: Icons.person_outline,
            child: ProfileScreen()),
      ],
    );
  }

  Widget _workspace() => RefreshIndicator(
        onRefresh: () async => _refresh(),
        child: ListView(
          padding: const EdgeInsets.all(24),
          children: [
            const LegacyPageTitle(
              'Administration',
              subtitle: 'Manage instructors, students and every class in the system.',
            ),
            const SizedBox(height: 20),
            FutureBuilder<_AdminData>(
              future: _data,
              builder: (context, snapshot) {
                if (snapshot.connectionState != ConnectionState.done) {
                  return const Center(
                      child: Padding(
                          padding: EdgeInsets.all(40),
                          child: CircularProgressIndicator(color: AppColors.gold)));
                }
                if (snapshot.hasError) {
                  return ErrorBanner((snapshot.error as ApiException).message);
                }
                final data = snapshot.data!;
                return Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Wrap(
                      spacing: 12,
                      runSpacing: 12,
                      children: [
                        _stat('Instructors', data.instructors.length),
                        _stat('Students', data.students.length),
                        _stat('Classes', data.classes.length),
                      ],
                    ),
                    const SizedBox(height: 18),
                    LegacySectionCard(
                      title: 'Records',
                      action: LegacyButton(
                          label: 'Add new', onPressed: () => _openEditor(data)),
                      children: [
                        Row(
                          children: [
                            _tabButton('Students', 'student'),
                            _tabButton('Instructors', 'instructor'),
                            _tabButton('Classes', 'classes'),
                          ],
                        ),
                        const SizedBox(height: 14),
                        _table(data),
                      ],
                    ),
                  ],
                );
              },
            ),
          ],
        ),
      );

  Widget _stat(String label, int value) => Container(
        width: 180,
        padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 14),
        decoration: BoxDecoration(
          color: AppColors.ink700,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: AppColors.line),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(label,
                style: const TextStyle(color: AppColors.muted, fontSize: 12)),
            const SizedBox(height: 4),
            Text('$value',
                style: const TextStyle(
                    color: AppColors.gold,
                    fontSize: 28,
                    fontWeight: FontWeight.w800)),
          ],
        ),
      );

  Widget _tabButton(String label, String value) {
    final active = _tab == value;
    return Padding(
      padding: const EdgeInsets.only(right: 8),
      child: OutlinedButton(
        onPressed: () => setState(() => _tab = value),
        style: OutlinedButton.styleFrom(
          foregroundColor: active ? AppColors.ink900 : AppColors.muted,
          backgroundColor: active ? AppColors.gold : Colors.transparent,
          side: BorderSide(color: active ? AppColors.gold : AppColors.line),
          minimumSize: const Size(0, 36),
          padding: const EdgeInsets.symmetric(horizontal: 14),
          textStyle: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700),
        ),
        child: Text(label),
      ),
    );
  }

  Widget _table(_AdminData data) {
    final isClass = _tab == 'classes';
    final rows = isClass
        ? data.classes
        : (_tab == 'instructor' ? data.instructors : data.students);
    if (rows.isEmpty) {
      return const Padding(
        padding: EdgeInsets.all(22),
        child: Text('No records yet.', style: TextStyle(color: AppColors.muted)),
      );
    }
    final headers = isClass
        ? ['Class', 'Code', 'Instructor', 'Students', 'Actions']
        : ['Name', 'Username', 'Section', 'Face', 'Actions'];
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      child: DataTable(
        headingRowColor: WidgetStateProperty.all(AppColors.ink800),
        border: TableBorder.all(color: AppColors.line, width: 1),
        columns: headers
            .map((h) => DataColumn(
                  label: Text(h,
                      style: const TextStyle(
                          color: AppColors.lime, fontWeight: FontWeight.w700)),
                ))
            .toList(),
        rows: rows.map((row) {
          return DataRow(cells: isClass ? _classCells(row, data) : _userCells(row));
        }).toList(),
      ),
    );
  }

  List<DataCell> _userCells(Map<String, dynamic> row) => [
        DataCell(Column(
          mainAxisAlignment: MainAxisAlignment.center,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('${row['full_name'] ?? ''}',
                style: const TextStyle(
                    color: AppColors.text, fontWeight: FontWeight.w700)),
            Text('${row['email'] ?? ''}',
                style: const TextStyle(color: AppColors.muted, fontSize: 11)),
          ],
        )),
        DataCell(Text('${row['username'] ?? ''}',
            style: const TextStyle(color: AppColors.text))),
        DataCell(Text('${row['section'] ?? '—'}',
            style: const TextStyle(color: AppColors.muted))),
        DataCell(Text(
          row['role'] == 'instructor'
              ? 'Not required'
              : (row['has_face'] == true ? 'Registered' : 'No photos'),
          style: TextStyle(
              color: row['has_face'] == true || row['role'] == 'instructor'
                  ? AppColors.leaf
                  : AppColors.gold),
        )),
        DataCell(Row(
          children: [
            TextButton(
                onPressed: () => _openEditor(_dummyData, row: row),
                child: const Text('Edit')),
            TextButton(
                onPressed: () => _delete('users', row),
                style: TextButton.styleFrom(foregroundColor: AppColors.danger),
                child: const Text('Delete')),
          ],
        )),
      ];

  List<DataCell> _classCells(Map<String, dynamic> row, _AdminData data) => [
        DataCell(Column(
          mainAxisAlignment: MainAxisAlignment.center,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('${row['name'] ?? ''}',
                style: const TextStyle(
                    color: AppColors.text, fontWeight: FontWeight.w700)),
            Text('${row['description'] ?? 'No description'}',
                style: const TextStyle(color: AppColors.muted, fontSize: 11)),
          ],
        )),
        DataCell(Text('${row['code'] ?? ''}',
            style: const TextStyle(color: AppColors.gold))),
        DataCell(Text('${row['instructor_name'] ?? 'Unassigned'}',
            style: const TextStyle(color: AppColors.text))),
        DataCell(Text('${row['student_count'] ?? 0}',
            style: const TextStyle(color: AppColors.text))),
        DataCell(Row(
          children: [
            TextButton(
                onPressed: () => _openEditor(data, row: row),
                child: const Text('Edit')),
            TextButton(
                onPressed: () => _delete('classes', row),
                style: TextButton.styleFrom(foregroundColor: AppColors.danger),
                child: const Text('Delete')),
          ],
        )),
      ];

  /// Editing a user only needs the instructor list for the class editor, so a
  /// lightweight placeholder avoids re-reading the data a second time.
  final _AdminData _dummyData = _AdminData([], [], []);
}

// ------------------------------------------------------------------- editor
class _AdminEditor extends StatefulWidget {
  const _AdminEditor({
    required this.isClass,
    required this.row,
    required this.tab,
    required this.instructors,
  });

  final bool isClass;
  final Map<String, dynamic>? row;
  final String tab;
  final List<Map<String, dynamic>> instructors;

  @override
  State<_AdminEditor> createState() => _AdminEditorState();
}

class _AdminEditorState extends State<_AdminEditor> {
  late final TextEditingController _name;
  late final TextEditingController _username;
  late final TextEditingController _email;
  late final TextEditingController _section;
  late final TextEditingController _password;
  late final TextEditingController _description;
  late String _role;
  int? _instructorId;
  bool _saving = false;
  String? _error;

  @override
  void initState() {
    super.initState();
    final row = widget.row;
    _name = TextEditingController(
        text: row?['full_name'] ?? row?['name'] ?? '');
    _username = TextEditingController(text: row?['username'] ?? '');
    _email = TextEditingController(text: row?['email'] ?? '');
    _section = TextEditingController(text: row?['section'] ?? '');
    _password = TextEditingController();
    _description = TextEditingController(text: row?['description'] ?? '');
    _role = row?['role'] ?? (widget.tab == 'instructor' ? 'instructor' : 'student');
    _instructorId = row?['instructor_id'] == null
        ? null
        : (row!['instructor_id'] as num).toInt();
  }

  @override
  void dispose() {
    for (final c in [
      _name,
      _username,
      _email,
      _section,
      _password,
      _description
    ]) {
      c.dispose();
    }
    super.dispose();
  }

  Future<void> _save() async {
    if (_name.text.trim().isEmpty) {
      setState(() => _error = 'Fill in the name field.');
      return;
    }
    setState(() {
      _saving = true;
      _error = null;
    });
    try {
      final editing = widget.row != null;
      if (widget.isClass) {
        final body = <String, dynamic>{
          'name': _name.text.trim(),
          'description': _description.text.trim(),
          'section': _section.text.trim().isEmpty ? 'General' : _section.text.trim(),
          'instructor_id': _instructorId,
        };
        if (editing) {
          await ApiClient.instance.put('/admin/classes/${widget.row!['id']}', body: body);
        } else {
          await ApiClient.instance.post('/admin/classes', body: body);
        }
      } else if (editing) {
        final body = <String, dynamic>{
          'name': _name.text.trim(),
          'username': _username.text.trim(),
          'email': _email.text.trim(),
          'role': _role,
          'section': _section.text.trim(),
          'school_id': widget.row!['school_id'] ?? '',
        };
        if (_password.text.isNotEmpty) body['password'] = _password.text;
        await ApiClient.instance.put('/admin/users/${widget.row!['id']}', body: body);
      } else {
        final body = <String, dynamic>{
          'full_name': _name.text.trim(),
          'username': _username.text.trim(),
          'email': _email.text.trim(),
          'role': _role,
          'section': _section.text.trim(),
        };
        if (_password.text.isNotEmpty) body['password'] = _password.text;
        await ApiClient.instance.post('/admin/users', body: body);
      }
      if (mounted) Navigator.of(context).pop(true);
    } on ApiException catch (e) {
      if (mounted) setState(() => _error = e.message);
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      backgroundColor: AppColors.ink800,
      title: Text(
        widget.isClass
            ? (widget.row == null ? 'Add class' : 'Rename / edit class')
            : (widget.row == null
                ? 'Add ${widget.tab == 'instructor' ? 'instructor' : 'student'}'
                : 'Edit account'),
        style: const TextStyle(color: AppColors.text),
      ),
      content: SizedBox(
        width: 520,
        child: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              if (widget.isClass) ...[
                AppTextField(
                    controller: _name, label: 'Class name', icon: Icons.class_outlined),
                const SizedBox(height: 12),
                AppTextField(
                    controller: _description,
                    label: 'Description',
                    icon: Icons.description_outlined),
                const SizedBox(height: 12),
                AppTextField(
                    controller: _section, label: 'Section', icon: Icons.groups_outlined),
                const SizedBox(height: 12),
                DropdownButtonFormField<int?>(
                  value: _instructorId,
                  hint: const Text('Unassigned'),
                  dropdownColor: AppColors.ink800,
                  decoration: const InputDecoration(
                    labelText: 'Instructor',
                    labelStyle: TextStyle(color: AppColors.label),
                    border: OutlineInputBorder(),
                  ),
                  items: [
                    const DropdownMenuItem<int?>(value: null, child: Text('Unassigned')),
                    ...widget.instructors.map((i) => DropdownMenuItem<int?>(
                          value: (i['id'] as num).toInt(),
                          child: Text('${i['full_name'] ?? ''}'),
                        )),
                  ],
                  onChanged: (value) => setState(() => _instructorId = value),
                ),
              ] else ...[
                Row(
                  children: [
                    Expanded(
                        child: AppTextField(
                            controller: _name, label: 'Full name')),
                    const SizedBox(width: 12),
                    Expanded(
                        child: AppTextField(
                            controller: _username, label: 'Username')),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                        child: AppTextField(
                            controller: _email, label: 'Email',
                            keyboardType: TextInputType.emailAddress)),
                    const SizedBox(width: 12),
                    Expanded(
                        child: AppTextField(
                            controller: _section, label: 'Section')),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: DropdownButtonFormField<String>(
                        value: _role,
                        dropdownColor: AppColors.ink800,
                        decoration: const InputDecoration(
                          labelText: 'Role',
                          labelStyle: TextStyle(color: AppColors.label),
                          border: OutlineInputBorder(),
                        ),
                        items: const [
                          DropdownMenuItem(value: 'student', child: Text('Student')),
                          DropdownMenuItem(
                              value: 'instructor', child: Text('Instructor')),
                        ],
                        onChanged: (value) =>
                            setState(() => _role = value ?? 'student'),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: AppTextField(
                        controller: _password,
                        label: widget.row == null
                            ? 'Password (default: Attendance123)'
                            : 'Password (blank keeps current)',
                        obscure: true,
                      ),
                    ),
                  ],
                ),
              ],
              if (_error != null) ...[
                const SizedBox(height: 14),
                ErrorBanner(_error!),
              ],
            ],
          ),
        ),
      ),
      actions: [
        TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Cancel')),
        LegacyButton(
            label: 'Save', busy: _saving, onPressed: _save),
      ],
    );
  }
}
