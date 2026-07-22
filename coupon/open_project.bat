@echo off
REM Replace the path below with the path to your VSCode executable
SET "vscodePath=C:\Users\Reza-PC\AppData\Local\Programs\Microsoft VS Code\Code.exe"

REM Open VSCode in the directory where the batch file is located
"%vscodePath%" "%~dp0"


