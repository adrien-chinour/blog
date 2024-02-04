import 'package:countly_flutter/countly_flutter.dart';
import 'package:logger/logger.dart' as logger;

class Logger {
  static final Logger _instance = Logger._internal();
  late final _internalLogger = logger.Logger(
    filter: null,
    output: null,
    printer: logger.PrettyPrinter(
      methodCount: 2,
      errorMethodCount: 8,
      lineLength: 120,
      colors: true,
      printEmojis: true,
      printTime: false,
    ),
  );

  factory Logger() {
    return _instance;
  }

  Logger._internal();

  void trace(dynamic message) {
    _internalLogger.t(message);
  }

  void debug(dynamic message) {
    _internalLogger.d(message);
  }

  void info(dynamic message) {
    _internalLogger.i(message);
  }

  void warning(dynamic message) {
    _internalLogger.w(message);
  }

  void error(dynamic message, {Error? error, StackTrace? stackTrace}) {
    Countly.logExceptionManual(message, true, stacktrace: stackTrace);
    _internalLogger.e(message, error: error, stackTrace: stackTrace);
  }

  void fatal(dynamic message, {Error? error, StackTrace? stackTrace}) {
    Countly.logExceptionManual(message, true, stacktrace: stackTrace);
    _internalLogger.f(message, error: error, stackTrace: stackTrace);
  }
}
