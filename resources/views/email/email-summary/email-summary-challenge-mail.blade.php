<!DOCTYPE html>
<html>
<head>

  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Labs Summary</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style type="text/css">
      body, table, td, a { -ms-text-size-adjust: 100%; /* 1 */ -webkit-text-size-adjust: 100%; /* 2 */ }
      table, td { mso-table-rspace: 0pt; mso-table-lspace: 0pt; }
      img { -ms-interpolation-mode: bicubic; }
      a[x-apple-data-detectors] { font-family: inherit !important; font-size: inherit !important; font-weight: inherit !important; line-height: inherit !important; color: inherit !important; text-decoration: none !important; }
      body { width: 100% !important; height: 100% !important; padding: 0 !important; margin: 0 !important; background-color: #f3f7fc; font-family: Arial, Helvetica, sans-serif; letter-spacing: -1px;}
      table { border-collapse: collapse !important; }
      a { color: #1a82e2; }
      img { height: auto; line-height: 100%; text-decoration: none; border: 0; outline: none; }
  </style>
</head>
<body>
  <!-- start body -->
  <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <!-- header start -->
    <tr>
      <td align="center">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 800px;">
          <tr>
            <td align="left" valign="middle" style="padding:15px;">

            </td>
          </tr>
        </table>
      </td>
    </tr>
    <!-- header end -->

    <!-- mid start -->
    <tr>
      <td align="center" >
        <table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#F8F8F8" style="max-width: 800px;">

          <tr>
            <td style="padding:20px 30px 10px; ">
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="center" width="5%"></td>
                    <td align="center">
                        <img style="width: 113px;max-width: 120px;" src="{{ config('site-settings.aws_url') }}front/img/logoNew.png"   border="0" style="max-width: 200px;"><br>
                        <div style="padding-top: 12px;color:#13A859;font-size: 29px;font-weight: bold;">{{ $summeryContent['ycs'] }}</div><br>
                        <div style="color: #777986;">{{ \Carbon\Carbon::createFromTimestamp(strtotime($summaryData['summary_date']['from']))->format('M d, Y')}} -  {{ \Carbon\Carbon::createFromTimestamp(strtotime($summaryData['summary_date']['to']))->format('M d, Y')}}</div><br>
                        <hr style="color:#D2D4DA;height: 1px;background-color: #D2D4DA;" align="center" width = "40%">
                        <div style="padding-top: 12px;font-size: 23px;"><img src="{{ config('site-settings.aws_url') }}front/img/complate.png" style="height: 19px;"> {{ $summeryContent['uhcted'] }} </div><br>
                    </td>
                    <td align="center" width="5%"> <img src="{{ config('site-settings.aws_url') }}front/img/email-bg-1.png"   style="max-height: 150px; max-width: 300px;"> </td>
                </tr>
              </table>
            </td>
          </tr>
          @if($summaryData['completed_module_counts']['challenges'] > 0)
          <tr>
            <td style="padding:20px 30px 10px; ">
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="center" width="30%">
                      <span style="font-size: 17px;font-weight: bold;" >{{ $summaryData['completed_module_counts']['challenges'] }}</span><br>
                      <span style="font-size: 17px;color: #777986">{{ $summeryContent['challnege'] }}</span>
                    </td>
                    <td align="center" width="30%">
                      <span style="font-size: 17px;font-weight: bold;" >{{ $summaryData['completed_module_counts']['challenge_paths'] }}</span><br>
                      <span style="font-size: 17px;color: #777986">{{ $summeryContent['challnegepath'] }} </span>
                    </td>
                    <td align="center" width="30%">
                      <span style="font-size: 17px;font-weight: bold;" >{{ $summaryData['completed_module_counts']['resources'] }}</span><br>
                      <span style="font-size: 17px;color: #777986">{{ $summeryContent['resource'] }}</span>
                    </td>
                </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td style="padding:20px 30px 10px; ">
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="center" width="30%">
                      <span style="font-size: 17px;font-weight: bold;" >{{ $summaryData['completed_module_counts']['achievements'] }}</span><br>
                      <span style="font-size: 17px;color: #777986">{{ $summeryContent['achievement'] }}</span>
                    </td>
                    <td align="center" width="30%">
                      <span style="font-size: 17px;font-weight: bold;" >{{ $summaryData['completed_module_counts']['learning_points'] }}</span><br>
                      <span style="font-size: 17px;color: #777986"> {{ $summeryContent['points'] }}</span>
                    </td>
                    <td align="center" width="30%">
                      <span style="font-size: 17px;font-weight: bold;" >{{ $summaryData['completed_module_counts']['verified_skills'] }}</span><br>
                      <span style="font-size: 17px;color: #777986">{{ $summeryContent['verifiedskills'] }}</span>
                    </td>
                </tr>
              </table>
            </td>
          </tr>
          @else
            <tr>
              <td style="padding:20px 30px 10px; ">
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                      <td align="center">
                          <div style="padding-top: 5px;font-size: 20px;color:#101223;"> {{ $summeryContent['emcauyds'] }}<br> {{ $summeryContent['proov'] }} </div><br>
                      </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td style="padding:20px 30px 10px; ">
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                      <td align="center">
                        <a href="https://dev.learnlab.ai/"><button type="button" class="button btn-success" style="border: none; margin-top: 10px;padding: 10px 10px 10px 10px;color: white;background-color: #13A859;border-radius: 6px;font-size: initial;"> {{ $summeryContent['explorechallenge'] }} </button> </a>
                      </td>
                  </tr>
                </table>
              </td>
            </tr>
          @endif

          <tr>
            <td style="padding:20px 30px 10px; ">
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>

                    <td align="center">
                        <hr style="color:#D2D4DA;height: 1px;background-color: #D2D4DA;" align="center" width = "40%">
                        <div style="padding-top: 12px;font-size: 23px;"><img src="{{ config('site-settings.aws_url') }}front/img/achievement.png" style="height: 19px;"> {{ $summeryContent['ttcamt'] }} </div><br>
                    </td>
                </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td style="padding:20px 30px 10px; ">
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                @if(!empty($summaryData['top_achievements']))
                  <tr>
                    <td align="center" width="30%">
                      <img src="{{ $summaryData['top_achievements'][0]['image'] }}"   style="width: 24%;"><br>
                      <span style="font-size: 17px;color: #777986">{{ $summaryData['top_achievements'][0]['name'] }} </span>
                    </td>
                    <td align="center" width="30%">
                      <img src="{{ $summaryData['top_achievements'][1]['image'] }}"   style="width: 24%;"><br>
                      <span style="font-size: 17px;color: #777986">{{ $summaryData['top_achievements'][1]['name'] }} </span>
                    </td>
                    <td align="center" width="30%">
                      <img src="{{ $summaryData['top_achievements'][2]['image'] }}"   style="width: 24%;"><br>
                      <span style="font-size: 17px;color: #777986">{{ $summaryData['top_achievements'][2]['name'] }} </span>
                    </td>
                  </tr>
                @else
                  <tr>
                    <td style="padding:0px 30px 10px; ">
                      <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td align="center">
                                <div style="padding-top: 5px;font-size: 20px;color:#101223;"> {{ $summeryContent['emcao'] }} <br> {{ $summeryContent['abctm'] }} </div><br>
                            </td>
                        </tr>
                        <tr>
                          <td align="center">
                            <a href="https://dev.learnlab.ai/"><button type="button" class="button btn-success" style="border: none; margin-top: 10px;padding: 10px 10px 10px 10px;color: white;background-color: #13A859;border-radius: 6px;font-size: initial;"> {{ $summeryContent['explore'] }} </button> </a>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                @endif
              </table>
            </td>
          </tr>

          @if(!empty($summaryData['top_achievements']))
          <tr>
            <td style="padding:20px 30px 10px; ">
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <td align="center" width="100%">
                  <a href="https://dev.learnlab.ai/"><button type="button" class="button btn-success" style="border: none; margin-top: 10px;padding: 10px 10px 10px 10px;color: white;background-color: #13A859;border-radius: 6px;font-size: initial;"> {{ $summeryContent['viewalla'] }} </button> </a>
                </td>
              </table>
            </td>
          </tr>
          @endif

          <tr>
            <td style="padding:0px 30px 10px; ">
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="center">
                        <hr style="color:#D2D4DA;height: 1px;background-color: #D2D4DA;" align="center" width = "40%">
                        <div style="padding-top: 5px;font-size: 23px;color:#13A859;">{{ $summeryContent['ttcyi'] }}   </div><br>
                        <div style="padding-top: 5px;font-size: 23px;color:#13A859;">{{ $summeryContent['withmost'] }} </div><br>
                    </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- Most interacted section start --}}
          @if(!empty($summaryData['most_interacted']))
           @foreach($summaryData['most_interacted'] as $key => $most_interacted)
              @php
                if ($most_interacted['module'] != 'challenge') {
                  continue;
                }
                $keyupdate = $key+1;
                $counti = '0'.$keyupdate;
              @endphp
             <tr>
               <td style="padding:20px 30px 10px; ">
                 <table border="0" cellpadding="0" cellspacing="0" width="100%">
                   <tr>
                       <td align="left" width="10%" style="padding: 0px 7px 22px 52px">
                         <div style="background-color: #13A859;height: 50px;width: 50px;margin-right: 10%;text-align: center;font-size: 35px; display: grid; place-items: center;"><span style="font-weight: bold;color: white;">{{ $counti }}</span></div>
                       </td>
                       <td align="left" width="90%">
                         <div style="font-size: 22px;text-align: initial;padding: 10px 10px 10px 10px;font-size: 17px;font-weight: bold;">{{ $most_interacted['title'] }}</div>
                         <div style="text-align: initial;padding: 10px 10px 10px 10px;font-size: 17px;color: #777986;">{{ $summeryContent['deadline'] }}: <span style="font-weight: bold;color: #101223;"> {{ $most_interacted['deadline'] }} </span>·  {{ $summeryContent['tsubmition'] }}: <span style="font-weight: bold;color: #101223;"> {{ $most_interacted['tsubmition'] }} </span>·{{ $summeryContent['status'] }}: <span style="font-weight: bold;color: #101223;"> {{ $most_interacted['status'] }} </span>· {{ $summeryContent['duration'] }}: <span style="font-weight: bold;color: #101223;"> {{ $most_interacted['duration'] }} </span>· {{ $summeryContent['level'] }}: <span style="font-weight: bold;color: #101223;"> {{ $most_interacted['level'] }} </span></div>
                         @if(!empty($most_interacted['achievementname']))
                              <div style="text-align: initial;padding: 0px 10px 0px 10px;font-size: 17px;color: #777986;">
                                @if($most_interacted['is_earnable'] == 'yes')
                                  {{ $summeryContent['ernable'] }}:
                                @else
                                  {{ $summeryContent['obtained'] }}:
                                @endif
                                <span style="font-weight: bold;color: #101223;">{{ $most_interacted['achievementname'] }} {{ $summeryContent['points'] }} </span>
                              </div>
                            @endif
                       </td>
                   </tr>
                 </table>
               </td>
             </tr>
           @endforeach
          @else
            <tr>
              <td>
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                    <td style="padding:20px 30px 10px; ">
                      <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td align="center">
                                <div style="padding-top: 5px;font-size: 20px;color:#101223;"> {{ $summeryContent['emcacue'] }} <br> {{ $summeryContent['yljnw'] }} </div><br>
                            </td>
                        </tr>
                      </table>
                    </td>
                    </tr>
                </table>
              </td>
            </tr>
         @endif
       {{-- Most interacted section end --}}

          <tr>
            <td style="padding:20px 30px 10px; ">
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="center">
                      <a href="https://dev.learnlab.ai/"><button type="button" class="button btn-success" style="border: none; margin-top: 10px;padding: 10px 10px 10px 10px;color: white;background-color: #13A859;border-radius: 6px;font-size: initial;"> {{ $summeryContent['explorechallenge'] }}</button> </a>
                        <hr style="color:#D2D4DA;height: 1px;background-color: #D2D4DA;" align="center" width = "40%">
                        <div style="padding-top: 5px;font-size: 23px;color:#13A859;">{{ $summeryContent['cymii'] }} </div><br>
                    </td>
                </tr>
              </table>
            </td>
          </tr>

            {{-- Most interested section start --}}
            @if(!empty($summaryData['most_interested']))
              @foreach($summaryData['most_interested'] as $key => $most_interested)
                <tr>
                  <td style="padding:20px 30px 10px; ">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                      <tr>
                          <td align="center" width="30%">
                            <img src="{{ $most_interested['cover_image'] }}"   style="max-height: 70px; max-width: 400px;">
                          </td>
                          <td align="center" width="70%">
                            <div style="font-size: 22px;text-align: initial;padding: 10px 10px 10px 10px;font-size: 17px;font-weight: bold;">{{ $most_interested['title'] }}</div>
                            <div style="text-align: initial;padding: 10px 10px 10px 10px;font-size: 17px;color: #777986;">{{ $summeryContent['deadline'] }}: <span style="font-weight: bold;color: #101223;"> {{ $most_interested['deadline'] }}</span>· {{ $summeryContent['duration'] }}: <span style="font-weight: bold;color: #101223;"> {{ $most_interested['duration'] }} </span>· {{ $summeryContent['level'] }}: <span style="font-weight: bold;color: #101223;"> {{ $most_interested['level'] }} </span></div>
                          </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              @endforeach
              @else
              <tr>
                <td style="padding:20px 30px 10px; ">
                  <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td align="center">
                            <div style="padding-top: 5px;font-size: 20px;color:#101223;"> {{ $summeryContent['notdata1'] }} <br> {{ $summeryContent['notdata2'] }} </div><br>
                        </td>
                    </tr>
                  </table>
                </td>
              </tr>
            @endif
          {{-- Most interested section end --}}

          <tr>
            <td style="padding:20px 30px 10px; ">
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="center">
                      <a href="https://dev.learnlab.ai/"><button type="button" class="button btn-success" style="border: none; margin-top: 10px;padding: 10px 10px 10px 10px;color: white;background-color: #13A859;border-radius: 6px;font-size: initial;"> {{ $summeryContent['explorechallenge'] }}</button> </a>
                    </td>
                </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td style="padding:20px 30px 10px; ">
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr style="font-size: 14px;">
                    <td align="left" width="30%"><img src="{{ config('site-settings.aws_url') }}front/img/email-bg-2.png"   style="max-width: 300px;"></td>
                    <td align="right" width="70%"><span style="color:#0D8D55; text-decoration: underline;"> support@prepr.org </span> |  ©@php   echo date('Y') @endphp Preprlabs. {{ $summeryContent['arr'] }}</br>
                    <p>{{ $summeryContent['tewst'] }} <span style="color:#0D8D55; text-decoration: underline;">{{ $user->email }}</span> {{ $summeryContent['byorc'] }}  <a href="https://dev.learnlab.ai/"> <span style="color:#0D8D55; text-decoration: underline;">{{ $summeryContent['unsubscribe'] }}</span></a></p>
                    </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <!-- mid end -->

    <!-- start footer -->
    <tr>
      <td align="center" style="padding:18px 0 0;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 800px;">
          <tr>
            <td align="center" style="padding: 25px 10px;  font-size: 16px; line-height: 16px; color: #000; letter-spacing: 0.1px;"></td>
          </tr>
        </table>
      </td>
    </tr>
    <!-- end footer -->

  </table>
</body>
</html>
