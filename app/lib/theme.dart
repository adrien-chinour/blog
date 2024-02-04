import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class BlogTheme {
  static ThemeData light() {
    return ThemeData(
      useMaterial3: true,
      brightness: Brightness.light,
      textTheme: TextTheme(
        titleLarge: GoogleFonts.dmSerifDisplay(
          fontSize: 42,
          fontWeight: FontWeight.w400,
          fontStyle: FontStyle.normal,
          height: 1,
        ),
        titleMedium: GoogleFonts.dmSerifDisplay(
          fontSize: 32,
          fontWeight: FontWeight.w400,
          fontStyle: FontStyle.normal,
          height: 1,
        ),
        titleSmall: GoogleFonts.dmSerifDisplay(
          fontSize: 16,
          fontWeight: FontWeight.w400,
          fontStyle: FontStyle.normal,
          height: 1.2,
        ),
      ),
    );
  }

  static ThemeData dark() {
    return ThemeData(
      useMaterial3: true,
      brightness: Brightness.dark,
      textTheme: TextTheme(
        titleLarge: GoogleFonts.dmSerifDisplay(
          fontSize: 42,
          fontWeight: FontWeight.w400,
          fontStyle: FontStyle.normal,
          height: 1,
        ),
        titleMedium: GoogleFonts.dmSerifDisplay(
          fontSize: 32,
          fontWeight: FontWeight.w400,
          fontStyle: FontStyle.normal,
          height: 1,
        ),
        titleSmall: GoogleFonts.dmSerifDisplay(
          fontSize: 16,
          fontWeight: FontWeight.w400,
          fontStyle: FontStyle.normal,
          height: 1.2,
        ),
      ),
    );
  }
}
