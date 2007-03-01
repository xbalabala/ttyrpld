
Name:           ttyrpld
Version:        2.18
Release:        0
Group:          System Environment/Daemons
Summary:        kernel-based tty screen- and keylogger
License:        LGPL/BSD
URL:            http://%name.sourceforge.net/

Source:         http://heanet.dl.sourceforge.net/sourceforge/%name/%name-%version.tar.bz2
BuildRoot:      %_tmppath/%name-%version-build
Prefix:         %_prefix
Requires:       libHX >= 1.8.0

%description
ttyrpld is a multi-os kernel-level tty keylogger and screenlogger with
(a)synchronous replay support.

THIS PACKAGE DOES NOT CONTAIN COMPILED KERNEL FILES -- you will have
to build the modified kernel and the rpldev module yourself. Sources
are provided in %prefix/src/%name-%version-km.
(Run `make -C %prefix/src/%name-%version-km/linux-2.6` after install,
and also be sure to have the Kernel sources installed properly!

Authors:
--------
  Jan Engelhardt <jengelh [at] gmx de>

%package doc
Group:          Documentation/HTML
Summary:        Documentation for ttyrpld

%description doc

%debug_package
%prep
%setup

%build
cd "$RPM_BUILD_DIR"/%name-%version/;
make EXT_CFLAGS="$RPM_OPT_FLAGS" DEBUG=0 all doc;

%install
b="%buildroot";
rm -Rf "$b";
make ROOT="$b" PREFIX="%prefix" install;
install -d "$b/usr/src/%name-%version-km/kpatch";
install -d "$b/usr/src/%name-%version-km/include";
cp -av k_linux-2.[46] "$b/usr/src/%name-%version-km/";
cp -av kpatch/linux* "$b/usr/src/%name-%version-km/kpatch/";
cp -av include/rpl_{ioctl,packet}.h "$b/usr/src/%name-%version-km/include/";
install -d "$b/%_sbindir";
ln -s %_sysconfdir/init.d/rpld "$b/%_sbindir/rcrpld";

%clean
rm -Rf "%buildroot";

%post
cat <<EOF
.---------------------------------------------------------------------.
|  Please do not forget to patch the kernel and compile the RPLDEV    |
|  kernel module if you have not done so yet. If they are up to date  |
|  (see CHANGES.txt), you of course do not need to recompile them.    |
\`---------------------------------------------------------------------'
EOF

%files
%defattr(-,root,root)
%config %_sysconfdir/rpld.conf
%_sysconfdir/init.d/*
%_bindir/*
%_sbindir/*
/usr/share/locale/*/LC_MESSAGES/%name.mo
/usr/src/%name-%version-km

%files doc
%doc doc/*.css doc/*.html doc/*.txt doc/*.png
