import 'package:flutter/material.dart';

import '../../theme/app_colors.dart';
import '../../widgets/clam_logo.dart';

/// The left-hand introduction panel from the original browser signup/login page.
class AuthHero extends StatelessWidget {
  const AuthHero({
    super.key,
    required this.title,
    required this.description,
    required this.points,
    this.footer,
  });

  final String title;
  final String description;
  final List<String> points;
  final Widget? footer;

  @override
  Widget build(BuildContext context) {
    return Container(
      color: AppColors.ink800,
      child: LayoutBuilder(
        builder: (context, constraints) {
          return SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 48, vertical: 40),
            child: ConstrainedBox(
              constraints: BoxConstraints(minHeight: constraints.maxHeight),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Row(
                    children: [
                      const ClamLogo(size: 54),
                      const SizedBox(width: 14),
                      RichText(
                        text: const TextSpan(
                          children: [
                            TextSpan(
                              text: 'AtClam\n',
                              style: TextStyle(
                                color: AppColors.text,
                                fontSize: 26,
                                fontWeight: FontWeight.w800,
                                height: 1,
                              ),
                            ),
                            TextSpan(
                              text: 'CLASSROOM ATTENDANCE',
                              style: TextStyle(
                                color: AppColors.muted,
                                fontSize: 11,
                                letterSpacing: 1.4,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 36),
                  Text(
                    title,
                    style: const TextStyle(
                      color: AppColors.text,
                      fontSize: 38,
                      fontWeight: FontWeight.w800,
                      height: 1.12,
                    ),
                  ),
                  const SizedBox(height: 18),
                  Text(
                    description,
                    style: const TextStyle(
                      color: AppColors.muted,
                      fontSize: 15,
                      height: 1.6,
                    ),
                  ),
                  const SizedBox(height: 30),
                  ...points.asMap().entries.map((e) => Padding(
                        padding: const EdgeInsets.only(bottom: 16),
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              '0${e.key + 1}',
                              style: const TextStyle(
                                color: AppColors.gold,
                                fontSize: 13,
                                fontWeight: FontWeight.w800,
                              ),
                            ),
                            const SizedBox(width: 14),
                            Expanded(
                              child: Text(
                                e.value,
                                style: const TextStyle(
                                  color: AppColors.text,
                                  fontSize: 14,
                                  height: 1.4,
                                ),
                              ),
                            ),
                          ],
                        ),
                      )),
                  if (footer != null) ...[
                    const SizedBox(height: 24),
                    footer!,
                  ],
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}

/// Responsive wrapper: hero on the left for desktop, hidden on small screens.
class AuthLayout extends StatelessWidget {
  const AuthLayout({super.key, required this.hero, required this.child});

  final Widget hero;
  final Widget child;

  @override
  Widget build(BuildContext context) {
    final wide = MediaQuery.sizeOf(context).width >= 900;
    return Scaffold(
      backgroundColor: AppColors.ink900,
      body: SafeArea(
        child: wide
            ? Row(
                children: [
                  SizedBox(width: 460, child: hero),
                  Expanded(
                    child: Center(
                      child: SingleChildScrollView(
                        padding: const EdgeInsets.all(48),
                        child: ConstrainedBox(
                          constraints: const BoxConstraints(maxWidth: 460),
                          child: child,
                        ),
                      ),
                    ),
                  ),
                ],
              )
            : SingleChildScrollView(
                padding: const EdgeInsets.all(24),
                child: ConstrainedBox(
                  constraints: const BoxConstraints(maxWidth: 460),
                  child: child,
                ),
              ),
      ),
    );
  }
}
