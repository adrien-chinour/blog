import 'package:blog_app/models/article.dart';
import 'package:flutter/material.dart';
import 'package:flutter_highlighter/flutter_highlighter.dart';
import 'package:flutter_highlighter/themes/atom-one-dark.dart';
import 'package:flutter_html/flutter_html.dart';
import 'package:url_launcher/url_launcher.dart';

class ArticleContent extends StatelessWidget {
  final Article article;

  const ArticleContent(this.article, {super.key});

  @override
  Widget build(BuildContext context) {
    ThemeData theme = Theme.of(context);

    return Html(
      data: article.content,
      extensions: [
        TagExtension(tagsToExtend: {'pre'}, builder: _buildPreTagExtension)
      ],
      onLinkTap: (url, attributes, element) {
        launchUrl(Uri.parse(url!));
      },
      style: {
        "p": Style(
          fontSize: FontSize(16.0)
        ),
        "blockquote": Style(
          padding: HtmlPaddings.all(8.0),
          margin: Margins.symmetric(vertical: 16.0),
          border: Border.all(color: theme.colorScheme.primary, width: 2.0),
        ),
        "code": Style(
          backgroundColor: theme.colorScheme.onBackground.withOpacity(0.1),
          padding: HtmlPaddings.symmetric(horizontal: 8.0),
        )
      },
    );
  }
}

Widget _buildPreTagExtension(ExtensionContext extensionContext) {
  var codeElement = extensionContext.elementChildren.first;

  String lang = RegExp('language-(?<name>[a-z]+)')
          .firstMatch(codeElement.classes.toString())!
          .namedGroup('name') ??
      'html';

  return SizedBox(
    width: double.infinity,
    child: HighlightView(
      codeElement.text,
      language: lang,
      theme: atomOneDarkTheme,
      padding: const EdgeInsets.all(8.0),
      tabSize: 2,
      textStyle: const TextStyle(fontSize: 12),
    ),
  );
}
