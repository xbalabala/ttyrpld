
Name:		ttyrpld
Version:	2.18.svn200
Release:	0
Group:		Productivity/Security
Summary:	Kernel-based tty screen- and keylogger
License:	LGPL/others
URL:		http://%name.sourceforge.net/

# Build KMP packages yes or no, 0 or 1
%define kernel  1

Source:		http://heanet.dl.sourceforge.net/sourceforge/%name/%name-%version.tar.bz2
BuildRoot:	%_tmppath/%name-%version-build
BuildRequires:	gettext-devel, libHX >= 1.10, php5, perl >= 5.8.0, w3m
%if 0%kernel
BuildRequires:	kernel-source, kernel-syms

%suse_kernel_module_package
%endif

%description
ttyrpld is a multi-os kernel-level tty keylogger and screenlogger with
(a)synchronous replay support.

Authors:
--------
	Jan Engelhardt <jengelh [at] gmx de>

%package doc
Group:          Documentation/HTML
Summary:        Documentation for ttyrpld

%description doc
Documentation for ttyrpld.

%if 0%kernel
%package KMP
Group:		Productivity/Security
Summary:	Kernel modules for ttyrpld

%description KMP
This package contains the ttyrpld kernel module (rpldev) for ttyrpld.
The rpldhk kernel module needs to be built into the kernel already.
%endif

%debug_package
%prep
%setup

%build
./autogen.sh;
%configure
make %{?jobs:-j%jobs};
mkdir obj;
ln -s ../include obj/include;
%if 0%kernel
	for flavor in %flavors_to_build; do
		rm -Rf "obj/$flavor";
		cp -r k_linux-2.6 "obj/$flavor";
		make -C "/usr/src/linux-obj/%_target_cpu/$flavor" M="$PWD/obj/$flavor";
	done;
%endif

%install
b="%buildroot";
rm -Rf "$b";
mkdir "$b";
make install DESTDIR="$b";
export INSTALL_MOD_PATH="$b";
%if 0%kernel
	for flavor in %flavors_to_build; do
		make -C "/usr/src/linux-obj/%_target_cpu/$flavor" \
			M="$PWD/obj/$flavor" modules_install;
	done;
%endif

%preun
%stop_on_removal rpld

%postun
%restart_on_update rpld
%insserv_cleanup

%files
%defattr(-,root,root)
%config %_sysconfdir/rpld.conf
%_sysconfdir/init.d/*
%_bindir/*
%_sbindir/*
%_datadir/locale/*/LC_MESSAGES/%name.mo

%files doc
%doc doc/*.css doc/*.html doc/*.txt doc/*.png

%changelog -n ttyrpld
