import 'package:blog_app/models/tag.dart';

final class Article {
  final String id;
  final String title;
  final String description;
  final String content;
  final Uri cover;
  final String slug;
  final DateTime publishAt;
  final List<Tag> tags;

  const Article({
    required this.id,
    required this.title,
    required this.description,
    required this.content,
    required this.cover,
    required this.slug,
    required this.publishAt,
    required this.tags,
});
}