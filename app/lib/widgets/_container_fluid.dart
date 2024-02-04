import 'package:flutter/widgets.dart';

class ContainerFluid extends StatelessWidget {
  final Widget child;

  const ContainerFluid({required this.child, super.key});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Container(
        constraints: const BoxConstraints(maxWidth: 1000),
        padding: const EdgeInsets.all(8.0),
        child: child,
      ),
    );
  }
}
