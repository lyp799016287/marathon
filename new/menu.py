# -*- coding: utf-8 -*-
from etc.setting import *

def create_menu():
    client = WeChatClient(APPID, APP_SECRET)
    client.menu.create({
        "button":[

            # {
            #     "type":"click",
            #     "name":"我的计划",
            #     "key":"V1001_MY_PLAN"
            # },
            {
                "type":"view",
                "name":"开始训练",
                "url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx4cbea4c0aa71212f&redirect_uri=http%3A%2F%2Fx.lares.me%2Ftraining&response_type=code&scope=snsapi_userinfo&state=training#wechat_redirect"
            },
            {
                "type":"view",
                "name":"我的计划",
                "url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx4cbea4c0aa71212f&redirect_uri=http%3A%2F%2Fx.lares.me%2Fplanlist&response_type=code&scope=snsapi_userinfo&state=planlist#wechat_redirect"
            },
            {
                "type":"view",
                "name":"我",
                "url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx4cbea4c0aa71212f&redirect_uri=http%3A%2F%2Fx.lares.me%2Fshowme&response_type=code&scope=snsapi_userinfo&state=showme#wechat_redirect"
            }
        ]
    })

if __name__ == "__main__":
    create_menu()
