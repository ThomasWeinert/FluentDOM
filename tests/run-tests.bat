@echo off
:LoopStart

"php.exe" -d safe_mode=Off "%PHPUNIT%" --verbose "FluentDomTest.php"

:LoopCondition
SET USRINPUT=y
ECHO .
SET /P USRINPUT=Run again (y/n - default y):

IF %USRINPUT% == n (
 ECHO Stopping
 GOTO LoopEnd
)
IF %USRINPUT% == y (
 GOTO LoopStart
) ELSE (
 GOTO LoopCondition
)

GOTO LoopStart
:LoopEnd