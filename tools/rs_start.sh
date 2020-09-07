echo mount drive
sudo mount -t tmpfs -o size=2500m,mode=1777 tmpfs /var/www/src
echo create dirs
mkdir /var/www/src/app
mkdir /var/www/src/bin
mkdir /var/www/src/dev
mkdir /var/www/src/generated
mkdir /var/www/src/import
mkdir /var/www/src/lib
mkdir /var/www/src/node_modules
mkdir /var/www/src/pub
mkdir /var/www/src/setup
mkdir /var/www/src/update
mkdir /var/www/src/var
mkdir /var/www/src/vendor
echo copy root
cp  ../src/* /var/www/src

echo explode bin
cp ../src/_data/bin.zip /var/www/src/bin/
(cd /var/www/src/bin  && unzip bin.zip >/dev/null)

echo explode dev
cp ../src/_data/dev.zip /var/www/src/dev/
(cd /var/www/src/dev  && unzip dev.zip >/dev/null)

echo explode lib
cp ../src/_data/lib.zip /var/www/src/lib/
(cd /var/www/src/lib  && unzip lib.zip >/dev/null)

echo explode node_modules
cp ../src/_data/node_modules.zip /var/www/src/node_modules/
(cd /var/www/src/node_modules  && unzip node_modules.zip >/dev/null)

echo explode pub
cp ../src/_data/pub.zip /var/www/src/pub/
(cd /var/www/src/pub  && unzip pub.zip >/dev/null)

echo explode setup
cp ../src/_data/setup.zip /var/www/src/setup/
(cd /var/www/src/setup  && unzip setup.zip >/dev/null)

echo explode update
cp ../src/_data/update.zip /var/www/src/update/
(cd /var/www/src/update  && unzip update.zip >/dev/null)

echo explode vendor
cp ../src/_data/vendor.zip /var/www/src/vendor/
(cd /var/www/src/vendor  && unzip vendor.zip >/dev/null)

cp  -afr  /mnt/hgfs/sf_projects/homegrown/src/app /var/www/src


