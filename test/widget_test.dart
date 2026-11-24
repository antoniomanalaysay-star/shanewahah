import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';

import 'package:at_clam/theme/app_colors.dart';

void main() {
  testWidgets('app uses the dark classroom palette', (tester) async {
    await tester.pumpWidget(
      MaterialApp(
        theme: buildAppTheme(),
        home: const Scaffold(body: SizedBox.expand()),
      ),
    );

    expect(
      Theme.of(tester.element(find.byType(Scaffold))).scaffoldBackgroundColor,
      AppColors.ink900,
    );
  });
}
