<?php //print_r($template_arg['reciever_name']);exit;?>
<!-- extract($data); -->
<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
   <head>
   </head>
   <body style="
      background-color: #d3d8de;
      ">
      <table style="
         max-width: 660px;
         margin: 0 auto;
         background-color: #fff;
         padding: 90px;
         height: 600px;
         color: #888d90;
         font-size: 15px;
         font-family: sans-serif;
         text-align: center;
         background-color: #f10a4a;
         background-image: url(bg.jpg);
         background-repeat: no-repeat;
         margin-top: 33px;
         background-size: cover;
         ">
         <tbody>
            <tr>
               <td>
                  <table style="
                     max-width: 660px;
                     margin: 0 auto;
                     background-color: #fff;
                     padding: 40px;
                     border-radius: 8px;
                     height: 600px;
                     color: #888d90;
                     font-size: 15px;
                     font-family: sans-serif;
                     text-align: center;
                     ">
                     <tbody>
                        <tr>
                           <td style="
                              font-size: 26px;
                              height: 54px;
                              color: #4f4fab;
                              vertical-align: middle;
                              ">Request for friendship </td>
                        </tr>
                        <tr>
                           <td style="
                              line-height: 28px;
                              color: #5a5959;
                              height: 39px;
                              vertical-align: bottom;
                              ">
                              Hi {{$template_arg['reciever_name']}},<br>
                              You had got a friend request from {{$template_arg['sender_name']}}.
                           </td>
                        </tr>
                        <tr>
                           <td style="
                              line-height: 28px;
                              color: #5a5959;
                              height: 79px;
                              vertical-align: middle;
                              ">
                              Please use the below links to accept or reject the request.<br>
                              <a href="http://localhost:8000/friend_action?action=2&token={{$template_arg['token']}}"><button style="padding: 5px 15px;color: #fff;background-color: #5cb85c;border-color: #4cae4c;margin-right: 15px;">Accept</button></a><a href="http://localhost:8000/friend_action?action=4&token={{$template_arg['token']}}"><button style="padding: 5px 15px;color: #fff;background-color: #ac2925;border-color: #761c19;margin-right: 15px;">Reject</button></a>
                           </td>
                        </tr>
                        <tr>
                           <td style="
                              font-style: italic;
                              color: #ff9d00;
                              font-size: 16px;
                              height: 72px;
                              vertical-align: middle;
                              "> 
                              Best Wishes from
                              Friends team !
                           </td>
                        </tr>
                        <tr>
                           <td style="
                              background-color: rgba(206, 205, 212, 0.59);
                              height: 40px;
                              border-radius: 5px;
                              color: #8a8686;
                              font-weight: normal !important;
                              font-size: 13px;
                              line-height: 22px;
                              "> If you think this email has reached you by mistake,<br> please ignore this email .</td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
         </tbody>
      </table>
   </body>
</html>