import 'package:blog_app/models/article.dart';
import 'package:blog_app/screens/article_screen.dart';
import 'package:blog_app/utils/env.dart';
import 'package:flutter/material.dart';

final class ArticleListItem extends StatelessWidget {
  final Article article;

  const ArticleListItem({required this.article, super.key});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      behavior: HitTestBehavior.opaque,
      onTap: () => Navigator.of(context).pushNamed(
        ArticleScreen.routeName,
        arguments: ArticleScreenArgs(article.id),
      ),
      child: Container(
        padding: const EdgeInsets.all(16.0),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Expanded(
              flex: 3,
              child: _ArticleTitle(article: article),
            ),
            Expanded(
              flex: 1,
              child: _ArticleCover(article: article),
            ),
          ],
        ),
      ),
    );
  }
}

final class _ArticleTitle extends StatelessWidget {
  final Article article;

  const _ArticleTitle({required this.article});

  @override
  Widget build(BuildContext context) {
    ThemeData theme = Theme.of(context);

    return Padding(
      padding: const EdgeInsets.only(right: 8.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.only(bottom: 12.0),
            child: Text(
              article.title,
              style: theme.textTheme.titleSmall,
            ),
          ),
          if (!Env.isMobile())
            Padding(
              padding: const EdgeInsets.only(bottom: 16),
              child: Text(article.description),
            ),
          _ArticleTagList(article.tags.map((tag) => tag.label).toList())
        ],
      ),
    );
  }
}

final class _ArticleCover extends StatelessWidget {
  final Article article;

  const _ArticleCover({required this.article});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(left: 8.0),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(4.0),
        child: AspectRatio(
          aspectRatio: 4 / 3,
          child: Hero(
            tag: article.cover.toString(),
            child: Image.network(
              article.cover.toString(),
              fit: BoxFit.fill,
            ),
          ),
        ),
      ),
    );
  }
}

final class _ArticleTagList extends StatelessWidget {
  final List<String> tags;

  const _ArticleTagList(this.tags);

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.start,
      children: tags.map((e) => _Tag(e)).toList(),
    );
  }
}

final class _Tag extends StatelessWidget {
  final String label;

  const _Tag(this.label);

  @override
  Widget build(BuildContext context) {
    ThemeData theme = Theme.of(context);

    return Container(
      margin: const EdgeInsets.only(right: 8.0),
      padding: const EdgeInsets.symmetric(horizontal: 4.0),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(4.0),
        color: theme.colorScheme.primary,
      ),
      child: Text(
        label,
        style: theme.textTheme.labelSmall!.copyWith(
          color: theme.colorScheme.background,
        ),
      ),
    );
  }
}
