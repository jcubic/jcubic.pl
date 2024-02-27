---
layout: post
title:  "Użycie monitoringu według scenariusza jako pomysł na test E2E"
date:   2024-02-22 14:04:53+0100
categories:
tags: testy e2e
author: adam_b
description: Czy usługa automatycznego monitoringu stron internetowych oferująca testowanie według przygotowanego scenariusza może być alternatywą dla testów E2E?
image:
 url: "/img/checklist.jpg"
 alt: "Czerwony marker oraz zaznaczone checkboxy na kartce papieru"
 attribution: "Image by [Freepik](https://www.freepik.com/free-photo/top-view-marked-checking-box_5330479.htm)"
 width: 800
 height: 450
---

Czy usługa automatycznego monitoringu stron internetowych oferująca testowanie według przygotowanego scenariusza może być alternatywą dla testów E2E?

<!-- more -->

## Co to jest test E2E?
Test E2E, czyli "End-to-End Testing", to rodzaj testu, który sprawdza działanie całego serwisu internetowego od początku do końca, z perspektywy użytkownika. Obejmuje on wszystkie etapy interakcji użytkownika ze stroną, od momentu otwarcia jej w przeglądarce aż do osiągnięcia finalnego celu. Test tego typu sprawdza wszystkie komponenty, zarówno część backendową, frontendową jak i sieciową. Nie pokaże nam co prawda dokładnego źródła problemu, ale da nam najważniejszą informację – czy ostatecznie wszystko działa tak jak należy.

## Klasyczne podejście
W typowym środowisku CI/CD testy E2E uruchamiane są automatycznie jako jeden z kroków pipeline’u, zazwyczaj na samym końcu, tuż przed deploymentem. Ponieważ testowana wersja systemu nie jest jeszcze dostępna w sieci, na czas wykonywania testu musi zostać uruchomiony tymczasowy serwer webowy. Spowoduje to, że serwis zostanie udostępniony w lokalnej sieci środowiska i będzie dostępny na potrzeby testu.

Mniej książkowy, ale nieco prostszy sposób to uruchomienie testów E2E zaraz po deploymencie do środowiska CI/CD. Prostszy, bo serwis jest już zbudowany i dostępny w sieci, omija nas więc konieczność jego tymczasowego hostowania na czas wykonywania testu. Minus takiego podejścia jest taki, że wynik testu nie ma bezpośredniego wpływu na to, czy dana wersja serwisu zostanie zdeployowana – bo została już wcześniej, przed uruchomieniem testu. W większości przypadków nie będzie to stanowiło dużego problemu bo na środowisku CI/CD akceptowalne jest to, że nie wszystko działa poprawnie.

## Wykorzystanie usługi monitoringu
Wiele współczesnych usług monitorowania stron internetowych ma możliwość testowania funkcjonalnego wedle szczegółowo zdefiniowanego scenariusza. Taki monitoring w zasadzie ma bardzo wiele wspólnych cech z testami E2E. Uruchamiane są w przeglądarce Chrome pozbawionej interfejsu i kontrolowanej przez Puppeteer, lub u ich podstaw leży Selenium. Definiowanie scenariusza natomiast przypomina proces programowania testów E2E – podzielony jest na kroki, gdzie każdy z nich określa albo jakąś interakcję ze stroną albo sprawdzenie efektu tych działań.

## Przykładowe użycie
Na przykładzie serwisu Ping.pl - który oferuje [monitorowanie strony](https://ping.pl) według scenariuszy - przygotujemy test wyszukiwarki w popularnym sklepie internetowym Morele.net.

Chcemy, aby nasz prosty test:
1. Otworzył stronę główną sklepu 
2. Wyszukał frazę „iphone”
3. Sprawdził czy pierwszy produkt w wynikach wyszukiwarki jest zgodny z oczekiwaniami i czy wyświetlony został przycisk „Dodaj do koszyka”

Tworzymy scenariusz wybierając odpowiednie kroki z kreatora, jednocześnie wspierając się konsolą deweloperską w przeglądarce w celu pobrania ze strony selektory elementów z którymi będziemy wchodzić w interakcje:

![Zrzut ekranu kroków scenariusza](/img/scenario-steps.png)
 
Tak przygotowany test będzie automatycznie uruchamiany z zadaną częstotliwością (nawet co 10 minut). W przypadku niepowodzenia podczas jego wykonywania, zostaniemy o tym fakcie poinformowani drogą mailową wraz ze zrzutem ekranu strony.

Takie podejście choć szybkie i wygodne, ma też swoje minusy. Ograniczeni jesteśmy do kroków scenariuszu, które dane narzędzie wspiera. W niektórych przypadkach może się okazać, że nie będą one wystarczające i przygotowanie testu, który zaplanujemy będzie trudne. Niemniej jednak w zdecydowanej większości przypadków, testy E2E zazwyczaj wykonują proste działania na stronie, takie jak wprowadzanie tekstu, klikanie przycisków, wysyłka formularzy itp. Dodatkowym wymaganiem jest to, że testowana wersja serwisu musi być publicznie dostępna w internecie, taki monitoring z oczywistych względów nie zadziała na prywatnej sieci.

## Podsumowanie
Zaprezentowane powyżej podejście do wykonywania testów E2E stron internetowych może być interesującą alternatywą dla klasycznie uruchamianych testów. Największym plusem takiego rozwiązania jest jego łatwe zastosowanie, brak konieczności utrzymywania oraz zintegrowane powiadomienia w przypadku wykrytych nieprawidłowości. Minusy? Ograniczenia co do rodzaju wykonywanych działań na stronie i fakt, że taki test może zostać wykonany dopiero po deploymencie danej wersji. Dodatkowo, testowany serwis musi być dostępny w internecie – nie w prywatnej sieci.
