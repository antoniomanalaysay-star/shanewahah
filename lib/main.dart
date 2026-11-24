import 'package:flutter/material.dart';

import 'config.dart';
import 'screens/home_gate.dart';
import 'theme/app_colors.dart';
import 'services/navigation.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await AppConfig.load();
  // HomeGate loads the demo roles; there is no saved login to restore.
  runApp(const AtClamApp());
}

class AtClamApp extends StatelessWidget {
  const AtClamApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'AT CLAM',
      debugShowCheckedModeBanner: false,
      navigatorKey: rootNavigatorKey,
      theme: buildAppTheme(),
      home: const HomeGate(),
    );
  }
}
