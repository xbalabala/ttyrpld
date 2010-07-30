
Name:		ttyrpld
Version:	2.60
Release:	0
Group:		Productivity/Security
Summary:	Kernel-based tty screen- and keylogger
License:	LGPL/others
URL:		http://ttyrpld.sf.net/

# Build KMP packages yes or no, 1 or 0
%define kernel  1

Source:		http://downloads.sf.net/ttyrpld/ttyrpld-%version.tar.bz2
BuildRoot:	%_tmppath/%name-%version-build
BuildRequires:	gettext-devel, libHX-devel >= 3.0
BuildRequires:	pkg-config >= 0.19
%if 0%kernel
BuildRequires:	kernel-syms

%kernel_module_package
%endif

%description
ttyrpld is a multi-os kernel-level tty keylogger and screenlogger with
(a)synchronous replay support.

Author(s):
----------
	Jan Engelhardt

%if 0%kernel
%package KMP
Group:		Productivity/Security
Summary:	Kernel modules for ttyrpld

%description KMP
This package contains the ttyrpld kernel module (rpldev) for ttyrpld.
The rpldhk kernel module needs to be built into the kernel already.
%endif

%prep
%setup -q

%build
if [ ! -e configure ]; then
	./autogen.sh;
fi;
%configure
make %{?_smp_mflags};
mkdir obj;
ln -s ../include obj/include;
%if 0%kernel
	for flavor in %flavors_to_build; do
		rm -Rf "obj/$flavor";
		cp -r k_linux-2.6 "obj/$flavor";
		make -C "/usr/src/linux-obj/%_target_cpu/$flavor" \
			M="$PWD/obj/$flavor" %{?_smp_mflags};
	done;
%endif

%install
b="%buildroot";
rm -Rf "$b";
mkdir -p "$b";
make install DESTDIR="$b";
export INSTALL_MOD_PATH="$b";
%if 0%kernel
	for flavor in %flavors_to_build; do
		make -C "/usr/src/linux-obj/%_target_cpu/$flavor" \
			M="$PWD/obj/$flavor" modules_install %{?_smp_mflags};
	done;
%endif

mkdir -p "$b/%_sbindir";
ln -s "%_initrddir/rpld" "$b/%_sbindir/rcrpld";

%preun
%stop_on_removal rpld

%post
%restart_on_update rpld

%postun
%insserv_cleanup

%files
%defattr(-,root,root)
%config %_sysconfdir/rpld.conf
%_sysconfdir/init.d/*
%_bindir/*
%_sbindir/*
%_datadir/locale/*/LC_MESSAGES/%name.mo
%_mandir/*/*

%changelog
* Mon Sep 21 2009 - jengelh
- package latest git snapshot so that we can move from libHX18 to libHX22
  and get fixes for Linux 2.6.31-RT
