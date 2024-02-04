import 'package:blog_app/api/interceptors/logger_interceptor.dart';
import 'package:blog_app/utils/env.dart';
import 'package:dio/dio.dart';
import 'package:dio_cache_interceptor/dio_cache_interceptor.dart';
import 'package:dio_cache_interceptor_hive_store/dio_cache_interceptor_hive_store.dart';
import 'package:path_provider/path_provider.dart';

abstract class AbstractApi {
  final Dio dio = Dio(
    BaseOptions(
      baseUrl: Env.getBlogUrl(),
      connectTimeout: const Duration(seconds: 10),
      receiveTimeout: const Duration(seconds: 30),
      validateStatus:  (int? status) => status! < 400,
    ),
  );

  AbstractApi() {
    getTemporaryDirectory().then((dir) {
      dio.interceptors.add(
        DioCacheInterceptor(
          options: CacheOptions(
            store: HiveCacheStore(dir.path),
          ),
        ),
      );
    });

    dio.interceptors.add(LoggerInterceptor());
  }
}
