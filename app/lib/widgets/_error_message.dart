import 'package:flutter/widgets.dart';

class ErrorMessage extends StatelessWidget {
  const ErrorMessage({super.key});

  @override
  Widget build(BuildContext context) {
    return const Center(child: Text('Oops une erreur est survenue'));
  }
}
