import 'package:flutter/material.dart';

import '../../services/api_client.dart';
import '../../theme/app_colors.dart';
import '../../widgets/app_widgets.dart';

const Map<String, Color> statusColors = {
  'present': Color(0xFF4CAF50),
  'late': Color(0xFFF5C518),
  'absent': Color(0xFFE53935),
  'excused': Color(0xFF42A5F5),
};

/// Monthly attendance calendar shared by instructors and students.
class AttendanceCalendar extends StatefulWidget {
  const AttendanceCalendar({
    super.key,
    required this.classId,
    this.studentId,
    this.studentName,
    this.canAddExcuse = false,
    this.onChanged,
  });

  final String classId;
  final int? studentId;
  final String? studentName;
  final bool canAddExcuse;
  final VoidCallback? onChanged;

  @override
  State<AttendanceCalendar> createState() => AttendanceCalendarState();
}

class AttendanceCalendarState extends State<AttendanceCalendar> {
  late DateTime _month;
  String? _filter;
  Map<String, dynamic>? _data;
  String? _error;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    final now = DateTime.now();
    _month = DateTime(now.year, now.month);
    load();
  }

  Future<void> load() async {
    setState(() => _loading = true);
    final query = <String, String>{
      'month': '${_month.year.toString().padLeft(4, '0')}-${_month.month.toString().padLeft(2, '0')}',
      if (widget.studentId != null) 'student_id': widget.studentId.toString(),
      if (_filter != null) 'status': _filter!,
    };
    try {
      final data = await ApiClient.instance
          .get('/attendance/class/${widget.classId}', query: query);
      if (mounted) setState(() => _data = data as Map<String, dynamic>);
    } on ApiException catch (e) {
      if (mounted) setState(() => _error = e.message);
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  void _shiftMonth(int delta) {
    setState(() => _month = DateTime(_month.year, _month.month + delta));
    load();
  }

  Future<void> _addExcuse() async {
    final day = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime(_month.year, _month.month),
      lastDate: DateTime(_month.year, _month.month + 1, 0),
    );
    if (day == null || !mounted) return;
    final noteController = TextEditingController();
    final saved = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Excuse letter'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text('For ${day.toIso8601String().substring(0, 10)}',
                style: const TextStyle(color: AppColors.muted)),
            const SizedBox(height: 12),
            AppTextField(
              controller: noteController,
              label: 'Reason',
              icon: Icons.note_alt_outlined,
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
    if (saved != true) return;
    try {
      await ApiClient.instance.post('/attendance/excuse', body: {
        'class_id': widget.classId,
        'date': day.toIso8601String().substring(0, 10),
        'note': noteController.text.trim(),
      });
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Excuse letter saved')));
        widget.onChanged?.call();
        load();
      }
    } on ApiException catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context)
            .showSnackBar(SnackBar(content: Text(e.message)));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final days = (_data?['days'] as Map?) ?? {};
    final summary = (_data?['summary'] as Map?) ?? {};
    final firstWeekday = DateTime(_month.year, _month.month, 1).weekday % 7;
    final totalDays = DateTime(_month.year, _month.month + 1, 0).day;
    final monthName = MaterialLocalizations.of(context).formatMonthYear(_month);

    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        if (widget.studentName != null)
          Padding(
            padding: const EdgeInsets.only(bottom: 10),
            child: Text('Attendance for ${widget.studentName}',
                style: const TextStyle(
                    color: AppColors.text, fontWeight: FontWeight.w700)),
          ),
        InfoCard(
          child: Column(
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  IconButton(
                    onPressed: () => _shiftMonth(-1),
                    icon: const Icon(Icons.chevron_left, color: AppColors.gold),
                  ),
                  Text(monthName,
                      style: const TextStyle(
                          color: AppColors.text,
                          fontSize: 17,
                          fontWeight: FontWeight.w700)),
                  IconButton(
                    onPressed: () => _shiftMonth(1),
                    icon: const Icon(Icons.chevron_right, color: AppColors.gold),
                  ),
                ],
              ),
              Wrap(
                spacing: 8,
                children: ['present', 'late', 'absent', 'excused'].map((s) {
                  final count = summary[s]?.toString() ?? '0';
                  return Chip(
                    avatar: CircleAvatar(backgroundColor: statusColors[s]),
                    label: Text('${_label(s)} $count',
                        style: const TextStyle(color: AppColors.text)),
                    backgroundColor: AppColors.ink800,
                  );
                }).toList(),
              ),
              const SizedBox(height: 8),
              DropdownButtonFormField<String?>(
                value: _filter,
                decoration: const InputDecoration(
                  labelText: 'Filter status',
                  labelStyle: TextStyle(color: AppColors.label),
                  enabledBorder: UnderlineInputBorder(
                      borderSide: BorderSide(color: AppColors.line)),
                ),
                dropdownColor: AppColors.ink800,
                items: [
                  const DropdownMenuItem(value: null, child: Text('All days')),
                  ...statusColors.keys.map((s) => DropdownMenuItem(
                        value: s,
                        child: Text(_label(s)),
                      )),
                ],
                onChanged: (value) {
                  setState(() => _filter = value);
                  load();
                },
              ),
            ],
          ),
        ),
        const SizedBox(height: 12),
        if (_loading)
          const Center(
              child: Padding(
                  padding: EdgeInsets.all(24),
                  child: CircularProgressIndicator(color: AppColors.gold)))
        else if (_error != null) ErrorBanner(_error!)
        else
          InfoCard(
            child: Column(
              children: [
                Row(
                  children: ['S', 'M', 'T', 'W', 'T', 'F', 'S']
                      .map((d) => Expanded(
                            child: Center(
                                child: Text(d,
                                    style: const TextStyle(
                                        color: AppColors.muted, fontSize: 12))),
                          ))
                      .toList(),
                ),
                const SizedBox(height: 6),
                GridView.builder(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 7, childAspectRatio: 1),
                  itemCount: firstWeekday + totalDays,
                  itemBuilder: (context, index) {
                    if (index < firstWeekday) return const SizedBox();
                    final day = index - firstWeekday + 1;
                    final key =
                        '${_month.year.toString().padLeft(4, '0')}-${_month.month.toString().padLeft(2, '0')}-${day.toString().padLeft(2, '0')}';
                    final info = (days[key] as Map?) ?? {};
                    final status = info['status'] as String?;
                    final color = status == null ? null : statusColors[status];
                    return Container(
                      margin: const EdgeInsets.all(2),
                      decoration: BoxDecoration(
                        color: color?.withAlpha(90) ?? AppColors.ink800,
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(
                            color: color ?? AppColors.line,
                            width: status == null ? 1 : 2),
                      ),
                      child: Center(
                        child: Text('$day',
                            style: const TextStyle(
                                color: AppColors.text, fontSize: 12)),
                      ),
                    );
                  },
                ),
              ],
            ),
          ),
        if (widget.canAddExcuse) ...[
          const SizedBox(height: 12),
          OutlinedButton.icon(
            onPressed: _addExcuse,
            icon: const Icon(Icons.note_alt_outlined),
            label: const Text('Add an excuse letter'),
            style: OutlinedButton.styleFrom(
                foregroundColor: AppColors.lime,
                side: const BorderSide(color: AppColors.line)),
          ),
        ],
      ],
    );
  }

  static String _label(String s) => switch (s) {
        'present' => 'Present',
        'late' => 'Late',
        'absent' => 'Absent',
        _ => 'Excused',
      };
}
