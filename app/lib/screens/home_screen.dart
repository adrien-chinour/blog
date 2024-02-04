import 'package:blog_app/api/article_api.dart';
import 'package:blog_app/models/article.dart';
import 'package:blog_app/widgets/_article_list_item.dart';
import 'package:blog_app/widgets/widgets.dart';
import 'package:flutter/material.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  static const routeName = '/';

  @override
  Widget build(BuildContext context) {
    ThemeData theme = Theme.of(context);

    return Scaffold(
      appBar: PreferredSize(
        preferredSize: const Size.fromHeight(70.0),
        child: AppBar(
          title: Center(
            child: Text('Undefined', style: theme.textTheme.titleMedium),
          ),
          backgroundColor: Colors.transparent,
          elevation: 0,
        ),
      ),
      body: ContainerFluid(
        child: FutureBuilder(
          future: ArticleApi().getArticles(limit: 10),
          builder: (context, snapshot) {
            if (snapshot.hasData) {
              List<Article> articles = snapshot.data!;

              return ListView.builder(
                itemCount: articles.length,
                itemBuilder: (context, index) {
                  return ArticleListItem(article: articles[index]);
                },
              );
            }

            if (snapshot.hasError) {
              return const ErrorMessage();
            }

            return const Loading();
          },
        ),
      ),
    );
  }
}
