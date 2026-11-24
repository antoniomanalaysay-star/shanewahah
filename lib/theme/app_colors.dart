import 'package:flutter/material.dart';

/// Green / gold palette (same look as your original login screen).
class AppColors {
  AppColors._();

  static const ink900 = Color(0xFF04100A); // deepest background
  static const ink800 = Color(0xFF071A10); // panels, app bar
  static const ink700 = Color(0xFF0A2415); // input fill, cards

  static const line = Color(0xFF1D5236); // borders
  static const leaf = Color(0xFF4CAF50);
  static const lime = Color(0xFFA8E063);
  static const forest = Color(0xFF1F7A43);
  static const gold = Color(0xFFF5C518);
  static const goldHi = Color(0xFFFFE066);

  static const text = Color(0xFFE7F3E9);
  static const muted = Color(0xFF8FAE99);
  static const label = Color(0xFF9CC4A8);
  static const hint = Color(0xFF5D7A68);
  static const danger = Color(0xFFFF6B6B);

  /// Colour from an RGB hex value (e.g. 0xF5C518) with an alpha from 0 to 1.
  static Color alpha(int rgb, double a) =>
      Color(0xFF000000 | rgb).withAlpha((a * 255).round());
}

ThemeData buildAppTheme() {
  final scheme = ColorScheme.fromSeed(
    seedColor: AppColors.forest,
    brightness: Brightness.dark,
  ).copyWith(
    primary: AppColors.gold,
    onPrimary: AppColors.ink900,
    surface: AppColors.ink800,
    onSurface: AppColors.text,
    error: AppColors.danger,
  );

  return ThemeData(
    useMaterial3: true,
    colorScheme: scheme,
    scaffoldBackgroundColor: AppColors.ink900,
    appBarTheme: const AppBarTheme(
      backgroundColor: AppColors.ink800,
      foregroundColor: AppColors.text,
      elevation: 0,
    ),
  );
}
