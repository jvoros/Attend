<?
echo "<h1>Production Migration</h1>";
R::wipe('checkin');
echo "Wiped checkins<br>";

R::wipe('conference');
echo "Wiped conferences<br>";

echo "Database ready for deployment.";