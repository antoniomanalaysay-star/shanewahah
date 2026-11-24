import 'dart:async';

import 'package:flutter/material.dart';

import '../../services/api_client.dart';
import '../../theme/app_colors.dart';

/// Bell + badge for announcements and excuse letters.
class NotificationBell extends StatefulWidget {
  const NotificationBell({super.key});

  @override
  State<NotificationBell> createState() => _NotificationBellState();
}

class _NotificationBellState extends State<NotificationBell> {
  Timer? _timer;
  int _unread = 0;
  List<dynamic> _items = [];

  @override
  void initState() {
    super.initState();
    _refresh();
    _timer = Timer.periodic(const Duration(seconds: 20), (_) => _refresh());
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  Future<void> _refresh() async {
    try {
      final data = await ApiClient.instance.get('/notifications');
      if (!mounted) return;
      setState(() {
        _unread = data['unread'] as int? ?? 0;
        _items = data['items'] as List? ?? [];
      });
    } on ApiException {
      // Staying silent avoids interrupting the user while they work.
    }
  }

  Future<void> _open() async {
    await _refresh();
    if (!mounted) return;
    await showModalBottomSheet(
      context: context,
      backgroundColor: AppColors.ink800,
      builder: (ctx) => SafeArea(
        child: Column(
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Padding(
                  padding: EdgeInsets.all(16),
                  child: Text('Notifications',
                      style: TextStyle(
                          color: AppColors.text,
                          fontSize: 18,
                          fontWeight: FontWeight.w700)),
                ),
                TextButton(
                  onPressed: () async {
                    await ApiClient.instance
                        .post('/notifications/read', body: {'all': true});
                    if (ctx.mounted) Navigator.pop(ctx);
                    _refresh();
                  },
                  child: const Text('Mark all read'),
                ),
              ],
            ),
            Expanded(
              child: _items.isEmpty
                  ? const Center(
                      child: Text('No notifications yet.',
                          style: TextStyle(color: AppColors.muted)))
                  : ListView.separated(
                      itemCount: _items.length,
                      separatorBuilder: (_, __) =>
                          const Divider(color: AppColors.line, height: 1),
                      itemBuilder: (context, index) {
                        final item = _items[index] as Map<String, dynamic>;
                        return ListTile(
                          leading: Icon(
                            item['kind'] == 'excuse'
                                ? Icons.mail_outline
                                : Icons.campaign_outlined,
                            color: item['read'] == true
                                ? AppColors.muted
                                : AppColors.gold,
                          ),
                          title: Text(item['title'] ?? '',
                              style: const TextStyle(color: AppColors.text)),
                          subtitle: Text(
                              '${item['body'] ?? ''} · ${item['time'] ?? ''}',
                              style: const TextStyle(color: AppColors.muted)),
                          onTap: () async {
                            await ApiClient.instance.post('/notifications/read',
                                body: {'id': item['id']});
                            if (ctx.mounted) Navigator.pop(ctx);
                            _refresh();
                          },
                        );
                      },
                    ),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Stack(
      alignment: Alignment.center,
      children: [
        IconButton(
          icon: const Icon(Icons.notifications_none_outlined),
          onPressed: _open,
          tooltip: 'Notifications',
        ),
        if (_unread > 0)
          Positioned(
            right: 6,
            top: 6,
            child: Container(
              padding: const EdgeInsets.all(2),
              decoration: const BoxDecoration(
                  color: AppColors.danger, shape: BoxShape.circle),
              constraints: const BoxConstraints(minWidth: 16, minHeight: 16),
              child: Text('$_unread',
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                      color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold)),
            ),
          ),
      ],
    );
  }
}
