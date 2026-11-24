import 'package:flutter/material.dart';

import '../theme/app_colors.dart';

/// The hexagon shield + graduation cap logo, drawn in code (no image file).
class ClamLogo extends StatelessWidget {
  const ClamLogo({super.key, this.size = 100});

  final double size;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: size,
      height: size,
      child: const CustomPaint(painter: _ClamLogoPainter()),
    );
  }
}

class _ClamLogoPainter extends CustomPainter {
  const _ClamLogoPainter();

  @override
  void paint(Canvas canvas, Size size) {
    canvas.save();
    canvas.scale(size.width / 120, size.height / 120);

    // Outer hex shield with gradient ring.
    final ring = Path()
      ..moveTo(60, 4)
      ..lineTo(108, 30)
      ..lineTo(108, 76)
      ..lineTo(60, 116)
      ..lineTo(12, 76)
      ..lineTo(12, 30)
      ..close();
    canvas.drawPath(
      ring,
      Paint()
        ..shader = const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFFFFE56B), Color(0xFFB9E04B), Color(0xFF1F7A43)],
          stops: [0, 0.42, 1],
        ).createShader(ring.getBounds()),
    );

    // Inner dark hex.
    final inner = Path()
      ..moveTo(60, 15)
      ..lineTo(98, 36)
      ..lineTo(98, 71)
      ..lineTo(60, 104)
      ..lineTo(22, 71)
      ..lineTo(22, 36)
      ..close();
    canvas.drawPath(inner, Paint()..color = const Color(0xFF05130C));

    // Graduation cap top.
    final cap = Path()
      ..moveTo(60, 34)
      ..lineTo(92, 47)
      ..lineTo(60, 60)
      ..lineTo(28, 47)
      ..close();
    canvas.drawPath(
      cap,
      Paint()
        ..shader = const LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [Color(0xFFFFE98A), Color(0xFFF5C518)],
        ).createShader(cap.getBounds()),
    );

    // Cap band.
    final band = Path()
      ..moveTo(39, 52.5)
      ..lineTo(39, 66)
      ..cubicTo(39, 71.2, 48.4, 74.8, 60, 74.8)
      ..cubicTo(71.6, 74.8, 81, 71.2, 81, 66)
      ..lineTo(81, 52.5);
    canvas.drawPath(
      band,
      Paint()
        ..style = PaintingStyle.stroke
        ..strokeWidth = 5
        ..strokeCap = StrokeCap.round
        ..color = const Color(0xFF8FD14F),
    );

    // Tassel.
    const tassel = Color(0xFFFFD93D);
    canvas.drawLine(
      const Offset(92, 47),
      const Offset(92, 62),
      Paint()
        ..style = PaintingStyle.stroke
        ..strokeWidth = 3
        ..strokeCap = StrokeCap.round
        ..color = tassel,
    );
    canvas.drawCircle(const Offset(92, 66), 4, Paint()..color = tassel);

    // Connection nodes.
    final nodes = Path()
      ..moveTo(45, 83)
      ..lineTo(60, 92)
      ..lineTo(75, 83);
    canvas.drawPath(
      nodes,
      Paint()
        ..style = PaintingStyle.stroke
        ..strokeWidth = 2.2
        ..strokeCap = StrokeCap.round
        ..strokeJoin = StrokeJoin.round
        ..color = AppColors.alpha(0xA8E063, 0.95),
    );
    canvas.drawCircle(const Offset(45, 83), 4.4, Paint()..color = AppColors.gold);
    canvas.drawCircle(const Offset(60, 92), 5, Paint()..color = AppColors.lime);
    canvas.drawCircle(const Offset(75, 83), 4.4, Paint()..color = AppColors.gold);

    canvas.restore();
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
