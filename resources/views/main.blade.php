@extends('layout.app')

@section('content')

    <x-header type="main" title="Аудит безопасности проекта" />

    <!-- About section-->
    <section id="about">
        <div class="container px-4">
            <div class="row gx-4 justify-content-center">
                <div class="col-lg-8">
                    <h2>О проекте</h2>
                    <p class="lead">Сбор отчетов, связанных с безопасностью проекта, при помощи утилит:</p>
                    <ul>
                        <li><a href="https://www.kali.org/tools/nmap/" target="_blank">nmap</a></li>
                        <li><a href="https://github.com/siberas/watobo" target="_blank">watobo</a></li>
                        <li><a href="https://kali.tools/?p=2295" target="_blank">Nikto</a></li>
                        <li><a href="https://kali.tools/?p=816" target="_blank">sqlmap</a></li>
                        <li><a href="https://www.kali.org/tools/sslscan/" target="_blank">Sslscan</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- Services section-->
    <section class="bg-light" id="services">
        <div class="container px-4">
            <div class="row gx-4 justify-content-center">
                <div class="col-lg-8">
                    <h2>Возможности</h2>
                    <ul>
                        <li>
                            Сбор сразу нескольких отчетов из разных утилит. В рамках сбора обного аудита
                        </li>
                        <li>
                            Лоудер статуса сбора репорта в телеграмме
                        </li>
                        <li>
                            Раздел с историей сборных отчетов для конкретного пользователя
                        </li>
                        <li>
                            Возможность пользователю регулярно получать отчеты по сохранённым проектам
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- Contact section-->
    <section id="contact">
        <div class="container px-4">
            <div class="row gx-4 justify-content-center">
                <div class="col-lg-8">
                    <h2>Контакты</h2>
                    <p class="lead"><a href="https://t.me/max_security_audit_bot" target="_blank">https://t.me/max_security_audit_bot</a> </p>
                </div>
            </div>
        </div>
    </section>
@endsection
