import 'package:blog_app/navigator/countly_observer.dart';
import 'package:blog_app/screens/article_screen.dart';
import 'package:blog_app/screens/home_screen.dart';
import 'package:blog_app/theme.dart';
import 'package:blog_app/utils/env.dart';
import 'package:blog_app/utils/logger.dart';
import 'package:countly_flutter/countly_flutter.dart';
import 'package:flutter/material.dart';
import 'package:intl/date_symbol_data_local.dart';

final CountlyObserver countlyObserver = CountlyObserver<PageRoute>();

void main() async {
  await initializeDateFormatting('fr_FR', null);
  await Env.init();
  runApp(const BlogApp());
}

class BlogApp extends StatelessWidget {
  const BlogApp({super.key});

  @override
  Widget build(BuildContext context) {

    try {
      Countly.isInitialized().then((bool isInitialized) {
        if (!isInitialized) {
          CountlyConfig config = CountlyConfig(
            Env.getCountlyHost(),
            Env.getCountlyKey(),
          );
          config.enableCrashReporting();

          Countly.initWithConfig(config);
        } else {
          Logger().debug('Countly already initialized');
        }
      });
    } catch (e) {
      Logger().error(e);
    }

    return MaterialApp(
      title: 'Undefined',

      // Routing
      initialRoute: HomeScreen.routeName,
      routes: {
        HomeScreen.routeName: (context) => const HomeScreen(),
        ArticleScreen.routeName: (context) => const ArticleScreen(),
      },
      navigatorObservers: [countlyObserver],

      // Theming
      theme: BlogTheme.light(),
      darkTheme: BlogTheme.dark(),
      themeMode: ThemeMode.dark,

      // Other
      debugShowCheckedModeBanner: false,
    );
  }
}
