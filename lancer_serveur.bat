@echo off
echo Démarrage du serveur de développement PHP...
echo Application disponible sur: http://localhost:8000
echo (Pour arrêter le serveur, fermez cette fenêtre ou appuyez sur Ctrl+C)

C:\xampp\php\php.exe -S localhost:8000 -t public

pause
