import 'package:blog_app/utils/logger.dart';
import 'package:dio/dio.dart';

class LoggerInterceptor implements Interceptor {
  @override
  void onError(DioException err, ErrorInterceptorHandler handler) {
    Logger().error(
      "Exception throw on Dio : ${err.message}",
      stackTrace: err.stackTrace,
    );

    handler.next(err);
  }

  @override
  void onRequest(RequestOptions options, RequestInterceptorHandler handler) {
    Logger().debug("Send request to ${options.path}");

    handler.next(options);
  }

  @override
  void onResponse(Response response, ResponseInterceptorHandler handler) {
    Logger().info(
      "Get response ${response.statusCode} for ${response.requestOptions.path}",
    );

    handler.next(response);
  }
}
