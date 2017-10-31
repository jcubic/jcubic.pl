---
layout: post
title:  "Jak zainstalować MacOSX na VirtualBoxie"
date:   2017-10-29 09:36:40+0100
categories:
tags:  przegladarki safari macosx virtualbox
author: jcubic
description: Jeśli piszesz aplikacje www dobrze jest ją przetestować na wszystkich przeglądarkach, czyli Firefox Opera, Chrome ale też na IE/Edge na windowsie oraz Safari na MacOSX.
---

Jeśli piszesz aplikacje www dobrze jest ją przetestować na wszystkich przeglądarkach czyli Firefox Opera, Chrome
ale też na IE/Edge na windowsie oraz Safari na MacOSX. Jest to problematyczne jeśli korzystasz z Systemu Operacyjnego
Linux lub Windows. Zainstalowanie Linuxa lub Windowsa na VirutalBoxie nie sprawia kłopotów, natomiast z MacOSX
może być już problem.

<!-- more -->

Aby zainstalować MacOSX musisz pobrać obraz dysku dla VirutalBoxa dla wersji High Sierra, 10.12,
z [google drive](https://goo.gl/ZEB4vB) ~6GB przygotowanym przez
[techsviewer.com](https://techsviewer.com/install-macos-sierra-virtualbox-windows/).
Jest to plik rar dlatego musisz go rozpakować, w tym celu pod Windowsem możesz użyć programu 7-Zip lub winrar.
Pod Linuxem musisz zainstalować program unrar i użyć komendy `unrar x plik.rar` z konsoli. Dobrze jest zachować
oryginalny plik rar na wszelki wypadek, jakby coś się zepsuło z twoją maszyną.

> Jeśli masz problem z pobraniem pliku (przez limit) możesz założyć konto google, a następnie utworzyć kopie pliku
> w swoim dysku i wtedy pobrać plik.

Następnie jeśli jeszcze nie zainstalowałeś VirtualBoxa musisz to zrobić, pod Linuxem (przynajmniej w Fedora i Ubuntu)
można go znaleźć w oficjalnych pakietach, jeśli go nie ma tutaj jest
[lista pakietów do pobrania](https://www.virtualbox.org/wiki/Linux_Downloads).

Pod Windowsem trzeba pobrać instalkę, można ją znaleźć na
[oficjalnej stronie VirtualBoxa](https://www.virtualbox.org/wiki/Downloads).

Po zainstalowaniu i uruchomieniu VirtualBoxa, musisz utworzyć nową maszynę z MacOSX High Sierra 10.13, wybierz
około połowy twojej fizycznej pamięci ram oraz opcje "użyj istniejącego pliku wirtualnego dysku twardego". Następnie
wybierz plik vmdk, który znajduje się w rozpakowanym pliku rar.

Po utworzeniu Maszyny musisz ustawić kilka opcji w ustawieniach maszyny:

1. W zakładce System/Płyta główna musisz mieć włączone EFI oraz Chipset ICH9 (powinny być już włączone).
2. W zakładce System/Procesor możesz ustawić połowę CPU które masz do wyboru, chyba że masz tylko jeden rdzeń oraz
   musisz mieć zaznaczoną opcje PAE/NX (powinna być już zaznaczona).
3. W zakładce Ekran/Ekran musisz ustawić 128MB pamięci wideo.

Następnie po zapisaniu, najlepiej zamknąć także program VirtualBox, musisz odpalić konsolę, pod Windowsem plik
cmd.exe jako Administrator i wykonaj poniższe polecenia (działają tylko dla wersji 5 VirtualBoxa):

Musisz wstawić swoją nazwę maszyny wirtualnej.

{% highlight PowerShell %}
cd "C:\Program Files\Oracle\VirtualBox\"
VBoxManage.exe modifyvm "Nazwa maszyny wirutalnej" --cpuidset 00000001 000106e5 00100800 0098e3fd bfebfbff
VBoxManage.exe setextradata "Nazwa maszyny wirutalnej" "VBoxInternal/Devices/efi/0/Config/DmiSystemProduct" "iMac11,3"
VBoxManage.exe setextradata "Nazwa maszyny wirutalnej" "VBoxInternal/Devices/efi/0/Config/DmiSystemVersion" "1.0"
VBoxManage.exe setextradata "Nazwa maszyny wirutalnej" "VBoxInternal/Devices/efi/0/Config/DmiBoardProduct" "Iloveapple"
VBoxManage.exe setextradata "Nazwa maszyny wirutalnej" "VBoxInternal/Devices/smc/0/Config/DeviceKey" "ourhardworkbythesewordsguardedpleasedontsteal(c)AppleComputerInc"
VBoxManage.exe setextradata "Nazwa maszyny wirutalnej" "VBoxInternal/Devices/smc/0/Config/GetKeyFromRealSMC" 1
{% endhighlight %}

Pod Linuxem nie trzeba używać `sudo` i wystarczy odpalić to samo, tylko nazwa pliku wykonywalnego jest z małych liter i bez końcówki ".exe"

{% highlight bash %}
vboxmanage modifyvm "Nazwa maszyny wirutalnej" --cpuidset 00000001 000106e5 00100800 0098e3fd bfebfbff
vboxmanage setextradata "Nazwa maszyny wirutalnej" "VBoxInternal/Devices/efi/0/Config/DmiSystemProduct" "iMac11,3"
vboxmanage setextradata "Nazwa maszyny wirutalnej" "VBoxInternal/Devices/efi/0/Config/DmiSystemVersion" "1.0"
vboxmanage setextradata "Nazwa maszyny wirutalnej" "VBoxInternal/Devices/efi/0/Config/DmiBoardProduct" "Iloveapple"
vboxmanage setextradata "Nazwa maszyny wirutalnej" "VBoxInternal/Devices/smc/0/Config/DeviceKey" "ourhardworkbythesewordsguardedpleasedontsteal(c)AppleComputerInc"
vboxmanage setextradata "Nazwa maszyny wirutalnej" "VBoxInternal/Devices/smc/0/Config/GetKeyFromRealSMC" 1
{% endhighlight %}

Teraz wystarczy tylko odpalić maszynę i już pod MacOSX wybrać kraj, klawiaturę, strefę czasową oraz wybrać użytkownika i hasło. Teraz możesz testować Safari pod MacOSX.

Jeśli dodatkowo chcesz testować pod iPhone/iPad możesz zainstalować iOS Simulator, aby mieć do niego dostęp, najpierw musisz zainstalować xcode (nie wiem może jest łatwiejszy sposób), czyli IDE do tworzenia aplikacji pod MacOSX oraz iOS.

Aby zainstalować xcode musisz otworzyć App Store (ikonka w docku na dole) i wyszukać xcode. Aby mieć dostęp do xcode musisz mieć apple ID aby go utworzyć możesz użyć tego [linka](https://appleid.apple.com/en_US/account) niestety musisz podać numer karty kredytowej, chociaż xcode jest za darmo. Ja nie podałem i wybrałem inny sposób.

Jeśli z jakiegoś powodu nie możesz zainstalować xcode z App Store, będziesz musiał zrobić to ręcznie. Tak jak w przypadku App Store musisz mieć Apple ID aby pobrać pliki xcode, ale nie musisz podawać numeru karty kredytowej. Dla wersji 10.13 wersja xcode to 9 możesz ją pobrać ze [strony apple](https://developer.apple.com/download/more/) plik zajmuje ~5GB. Warto pobrać go na nasz normalny system, żeby nie pobierać jeszcze raz jak coś pójdzie nie tak z naszą instalacją. Aby przesłać plik z Systemu hosta do maszyny wirtualnej można skorzystać z jakiegoś serwera np. http (jeśli piszesz aplikacje www to już pewnie masz zainstalowanego Apache-a albo inny serwer www) i skorzystać z adresu IP: `10.0.2.2`.

Po pobraniu pliku xip, musisz go rozpakować, nastąpi to automatycznie jeśli klikniesz na plik. Po rozpakowaniu będziesz miał plik xcode w tym samym katalogu, w którym był plik xip.

Po uruchomieniu xcode zainstaluje się w naszym systemie. Po instalacji będziemy mogli uruchomić korzystając z tego samego pliku, którym go zainstalowaliśmy. Po uruchomieni pojawi się ikonka w docku, jeśli klikniesz na nią prawym klawiszem myszy będziesz miał dostęp, w menu "Open Developer Tools", do aplikacji Simulator.

> UWAGA: nie powinno się robić update-u. Przynajmniej mi się nie udało potem odpalić systemu.
