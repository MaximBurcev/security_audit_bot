@extends('layout.app')



@section('header')
    <!-- Header-->
    <header class="bg-primary bg-gradient text-white">
        <div class="container px-4 text-center">
            <h1 class="fw-bolder">Аудит безопасности проекта</h1>
            <a class="btn btn-lg btn-light" href="#about">Узнай больше!</a>
        </div>
    </header>
@endsection

@section('content')
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
                    <p class="lead">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vero odio fugiat
                        voluptatem dolor, provident officiis, id iusto! Obcaecati incidunt, qui nihil beatae magnam et
                        repudiandae ipsa exercitationem, in, quo totam.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
