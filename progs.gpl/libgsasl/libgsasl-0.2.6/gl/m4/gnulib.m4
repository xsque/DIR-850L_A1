# Copyright (C) 2004 Free Software Foundation, Inc.
# This file is free software, distributed under the terms of the GNU
# General Public License.  As a special exception to the GNU General
# Public License, this file may be distributed as part of a program
# that contains a configuration script generated by Autoconf, under
# the same distribution terms as the rest of that program.
#
# Generated by gnulib-tool.
#
# Invoked as: gnulib-tool --import
# Reproduce by: gnulib-tool --import --dir=. --lib=libgl --source-base=gl --m4-base=gl/m4 --aux-dir=. --libtool --lgpl alloca-opt base64 getdelim getline gettext gettext-h restrict size_max stdbool strdup strverscmp vasnprintf vasprintf xsize

AC_DEFUN([gl_EARLY],
[
  AC_GNU_SOURCE
])

AC_DEFUN([gl_INIT],
[
  gl_FUNC_ALLOCA
  gl_FUNC_BASE64
  gl_FUNC_GETDELIM
  gl_FUNC_GETLINE
  dnl you must add AM_GNU_GETTEXT([external]) or similar to configure.ac.
  gl_C_RESTRICT
  gl_SIZE_MAX
  AM_STDBOOL_H
  gl_FUNC_STRDUP
  gl_FUNC_STRVERSCMP
  gl_FUNC_VASNPRINTF
  gl_FUNC_VASPRINTF
  gl_XSIZE
])

dnl Usage: gl_MODULES(module1 module2 ...)
AC_DEFUN([gl_MODULES], [])

dnl Usage: gl_SOURCE_BASE(DIR)
AC_DEFUN([gl_SOURCE_BASE], [])

dnl Usage: gl_M4_BASE(DIR)
AC_DEFUN([gl_M4_BASE], [])

dnl Usage: gl_LIB(LIBNAME)
AC_DEFUN([gl_LIB], [])

dnl Usage: gl_LGPL
AC_DEFUN([gl_LGPL], [])

# gnulib.m4 ends here
