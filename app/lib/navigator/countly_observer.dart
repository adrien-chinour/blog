import 'package:blog_app/utils/logger.dart';
import 'package:countly_flutter/countly_flutter.dart';
import 'package:flutter/material.dart';

/// RouteObserver for Countly
/// This observer track route navigation
final class CountlyObserver<R extends Route<dynamic>> extends RouteObserver<R> {
  @override
  void didPush(Route route, Route? previousRoute) {
    super.didPush(route, previousRoute);
    _log(route);
  }

  @override
  void didPop(Route route, Route? previousRoute) {
    super.didPop(route, previousRoute);
    _log(previousRoute!);
  }

  void _log(Route route) {
    try {
      String routeName = _routeName(route);
      Logger().debug("CountlyObserver didPush trigger for route=$routeName");
      Countly.instance.views.startAutoStoppedView(routeName);
    } catch (e) {
      Logger().warning('Tracking user view with Countly failed ($e)');
    }
  }

  String _routeName(Route route) {
    return route.settings.arguments != null
        ? "${route.settings.name}?${route.settings.arguments}"
        : route.settings.name ?? 'unknown';
  }
}
