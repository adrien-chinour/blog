import 'dart:io' show Platform;
import 'package:flutter_dotenv/flutter_dotenv.dart';

class Env {
  static Future init() async {
    await dotenv.load(fileName: ".env");
  }

  static String getBlogUrl() {
    return dotenv.get('BLOG_URL');
  }

  static String getCountlyHost() {
    return dotenv.get('COUNTLY_HOST');
  }

  static String getCountlyKey() {
    return dotenv.get('COUNTLY_KEY');
  }

  static bool isMobile() {
    return Platform.isIOS || Platform.isAndroid;
  }
}
