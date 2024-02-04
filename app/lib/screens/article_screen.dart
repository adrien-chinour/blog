import 'package:blog_app/api/article_api.dart';
import 'package:blog_app/widgets/widgets.dart';
import 'package:flutter/material.dart';

final class ArticleScreenArgs {
  final String identifier;

  ArticleScreenArgs(this.identifier);

  @override
  String toString() {
    return "identifier=$identifier";
  }
}

class ArticleScreen extends StatelessWidget {
  const ArticleScreen({super.key});

  static const routeName = '/article';

  @override
  Widget build(BuildContext context) {
    var args = ModalRoute.of(context)!.settings.arguments as ArticleScreenArgs;

    return Scaffold(
      appBar: AppBar(
        leading: BackButton(onPressed: () => Navigator.pop(context)),
        backgroundColor: Colors.transparent,
        elevation: 0,
      ),
      body: FutureBuilder(
        future: ArticleApi().getArticle(args.identifier),
        builder: (context, snapshot) {
          if (snapshot.hasData) {
            return SingleChildScrollView(
              child: ContainerFluid(
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      ArticleHeader(snapshot.data!),
                      ArticleContent(snapshot.data!),
                    ],
                  ),
                ),
              ),
            );
          }

          if (snapshot.hasError) {
            return const ErrorMessage();
          }

          return const Loading();
        },
      ),
    );
  }
}
