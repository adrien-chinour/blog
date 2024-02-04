import 'package:blog_app/api/abstract_api.dart';
import 'package:blog_app/models/article.dart';
import 'package:blog_app/models/tag.dart';
import 'package:blog_app/utils/logger.dart';

final class ArticleApi extends AbstractApi {
  static final ArticleApi _instance = ArticleApi._internal();

  factory ArticleApi() {
    return _instance;
  }

  ArticleApi._internal();

  Future<List<Article>> getArticles({int limit = 10}) async {
    final response = await dio.get('/api/articles');

    final List<Article> articles = [];
    for (var article in response.data) {
      try {
        articles.add(_buildFromMap(article));
      } catch (e) {
        Logger().error("Ignore article due to exception on Denormalize");
      }
    }

    return articles;
  }

  Future<Article> getArticle(String identifier) async {
    final response = await dio.get('/api/articles/$identifier');

    return _buildFromMap(response.data);
  }
}

Article _buildFromMap(Map<String, dynamic> data) {
  List<Tag> tags = [];
  try {
    for (var tag in data['tags']) {
      tags.add(Tag(id: tag['id'], label: tag['name']));
    }
  } catch (e) {
    Logger().error("Ignore tags due to exception on Denormalize");
  }

  try {
    return Article(
      id: data['id'],
      title: data['title'],
      description: data['description'],
      content: data['content'],
      cover: Uri.parse(data['imageUrl']),
      slug: data['slug'],
      publishAt: DateTime.parse(data['publicationDate']),
      tags: tags,
    );
  } catch (e) {
    Logger().fatal(e);
    throw ArticleDenormalizeError();
  }
}

class ArticleDenormalizeError extends Error {}
