# Usage:
#     perl -w find_nonlocalized.pl [<directory> ...]
#
# Scans .m and .mm files for potentially nonlocalized
#   strings that should be.
# Lines marked with DNL (Do Not Localize) are ignored.
# String constant assignments of this form are ignored if
#   they have no spaces in the value:
#   NSString * const <...> = @"...";
# Strings on the same line as NSLocalizedString are
#   ignored.
# Certain common methods that take nonlocalized strings are
#   ignored
# URLs are ignored
#
# Exits with 1 if there were strings found
use File::Basename;
use File::Find;
use strict;
# Include the basenames of any files to ignore
my @EXCLUDE_FILENAMES = qw();
# Regular expressions to ignore
my @EXCLUDE_REGEXES = (
qr/\bDNL\b/,
qr/NSLocalizedString/,
qr/NSString\s*\*\s*const\s[^@]*@"[^ ]*";/,
qr/NSLog\(/,
qr/@"http/, qr/@"mailto/, qr/@"ldap/,
qr/predicateWithFormat:@"/,
qr/Key(?:[pP]ath)?:@"/,
qr/setDateFormat:@"/,
qr/NSAssert/,
qr/imageNamed:@"/,
qr/NibNamed?:@"/,
qr/pathForResource:@"/,
qr/fileURLWithPath:@"/,
qr/fontWithName:@"/,
qr/stringByAppendingPathComponent:@"/,
);
my $FoundNonLocalized = 0;
sub find_nonlocalized {
    return unless $File::Find::name =~ /\.mm?$/;
    return if grep($_, @EXCLUDE_FILENAMES);
    open(FILE, $_);
LINE:
    while (<FILE>) {
        if (/@"[^"]*[a-z]{3,}/) {
            foreach my $regex (@EXCLUDE_REGEXES) {
                next LINE if $_ =~ $regex;
            }
            print "$File::Find::name:$.:$_";
            $FoundNonLocalized = 1;
        }
    }
    close(FILE);
}
my @dirs = scalar @ARGV ? @ARGV : (".");
find(\&find_nonlocalized, @dirs);
exit $FoundNonLocalized ? 1 : 0;