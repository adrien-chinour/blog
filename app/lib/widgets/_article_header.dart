import 'package:blog_app/models/article.dart';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

class ArticleHeader extends StatelessWidget {
  final Article article;

  const ArticleHeader(this.article, {super.key});

  @override
  Widget build(BuildContext context) {
    ThemeData theme = Theme.of(context);

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          "Publié le ${DateFormat('d MMM y', 'FR_fr').format(article.publishAt)}".toUpperCase(),
          style: theme.textTheme.labelMedium!.copyWith(
            color: theme.colorScheme.onBackground.withOpacity(0.7),
          ),
        ),
        Padding(
          padding: const EdgeInsets.symmetric(vertical: 16.0),
          child: Text(article.title, style: theme.textTheme.titleMedium),
        ),
        Padding(
          padding: const EdgeInsets.symmetric(vertical: 8.0),
          child: Text(article.description, style: theme.textTheme.bodyLarge),
        ),
        Padding(
          padding: const EdgeInsets.symmetric(vertical: 8.0),
          child: ClipRRect(
            borderRadius: BorderRadius.circular(4.0),
            child: Hero(
              tag: article.cover.toString(),
              child: Image.network(article.cover.toString()),
            ),
          ),
        )
      ],
    );
  }
}
