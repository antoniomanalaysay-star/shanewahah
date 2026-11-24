import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../models/app_user.dart';
import 'api_client.dart';

/// The active identity in this no-login build.
///
/// There is no token and no login screen: the role picker selects one of the
/// demo accounts and the backend identifies it from ApiClient's demo header.
class Session extends ChangeNotifier {
  Session._();

  static final Session instance = Session._();

  static const String _lastRoleKey = 'demo_role';
  static const List<String> _roleOrder = ['student', 'instructor', 'admin'];

  final ApiClient _api = ApiClient.instance;

  AppUser? user;
  bool ready = false;
  List<AppUser> demoUsers = [];

  /// Load the three demo roles and reopen the last one used, if any.
  Future<void> loadDemoUsers() async {
    final data = await _api.get('/demo/users') as Map<String, dynamic>;
    demoUsers = data.values
        .map((e) => AppUser.fromJson(e as Map<String, dynamic>))
        .toList();
    demoUsers.sort((a, b) =>
        _roleOrder.indexOf(a.role).compareTo(_roleOrder.indexOf(b.role)));
    ready = true;

    final prefs = await SharedPreferences.getInstance();
    final lastRole = prefs.getString(_lastRoleKey);
    if (lastRole != null) {
      for (final candidate in demoUsers) {
        if (candidate.role == lastRole) {
          await _apply(candidate);
          break;
        }
      }
    }
    notifyListeners();
  }

  Future<void> enterDemo(AppUser demoUser) async {
    await _apply(demoUser);
    notifyListeners();
  }

  /// "Log out" in this build just returns to the role picker.
  void switchRole() {
    _api.demoUserId = null;
    user = null;
    notifyListeners();
  }

  /// Re-fetch the account after a profile / face-photo change.
  Future<void> refresh() async {
    final data = await _api.get('/auth/me') as Map<String, dynamic>;
    user = AppUser.fromJson(data);
    notifyListeners();
  }

  Future<void> _apply(AppUser demoUser) async {
    _api.demoUserId = demoUser.id.toString();
    user = demoUser;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_lastRoleKey, demoUser.role);
  }
}
