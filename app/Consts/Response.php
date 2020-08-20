<?php
/**
 * Created by PhpStorm.
 * User: liulei
 * Date: 2020/8/20
 * Time: 3:40 PM
 */

namespace App\Consts;


class Response
{
    const NOT_LOGIN								= 1000000;
    const UN_PW_CAN_NOT_EMPTY                   = 1000001;
    const UN_PW_IS_WRONG                        = 1000002;
    const ACCOUNT_IS_EXIST                      = 1000003;
    const NO_PRIVILAGE                          = 1000004;
    const NO_DATA                               = 1000005;
    const DATA_IS_EXIST                         = 1000006;
    const OLD_PW_IS_WRONG                       = 1000007;
    const PARAM_IS_WRONG                        = 1000008;
    const FILETYPE_IS_WRONG                     = 1000009;
    const VCODE_IS_WRONG                        = 1000010;
    const ERR_TIMES_GT_THREE                    = 1000011;
    const ERR_TIMES_GT_SIX                      = 1000012;
    const UN_NOT_EXIST                          = 1000013;
    const PW_IS_WRONG                           = 1000014;
    const VCODE_IS_EMPTY                        = 1000015;
    const FREEZED                               = 1000016;
    const SMS_CODE_ERR                          = 1000017;
    const LOCKED                                = 1000018;
    const INACTIVE                              = 1000019;
    const PW_NOT_SAME                           = 1000020;
    const OUT_OF_NUM                            = 1000021;
    const RELOGIN_ERR                           = 1000022;
    const VCODE_TIMEOUT                         = 1000023;
    const SMS_CODE_TIMEOUT                      = 1000024;
    const SMS_SEND_ERR                          = 1000025;
    const NOT_TIME                              = 1000026;
    const ALREADY_IN_ROOM                       = 1000027;
    const UNEXCEPTION                           = 1000028;
    const INVALID_REQUEST                       = 1000029;
    const LOGIN_FAILED                          = 1000030;


    const UNDER_CTRL                            = 2000001;

    const UPDATE_ERR                            = 3000001;
    const INSERT_ERR                            = 3000002;
    const DEL_ERR                               = 3000003;
    const READ_ERR                              = 3000004;

    // 写代码也有趣味性
    const GO_DIE                                = 'go_die';
    const IAM_NONAME                            = 'im_noname';
    const NOT_UR_BUSINESS                       = 'not_ur_business';

    const ERROR_MESSAGES = array(
        self::NOT_LOGIN                         => '未登录',
        self::UN_PW_CAN_NOT_EMPTY               => '用户名或密码不能为空',
        self::UN_PW_IS_WRONG                    => '用户名或密码错误',
        self::ACCOUNT_IS_EXIST                  => '该帐号已存在',
        self::NO_PRIVILAGE                      => '无权限',
        self::NO_DATA                           => '无数据',
        self::DATA_IS_EXIST                     => '该数据已存在',
        self::OLD_PW_IS_WRONG                   => '输入的旧密码不正确',
        self::PARAM_IS_WRONG                    => '参数错误',
        self::FILETYPE_IS_WRONG                 => '文件类型错误',
        self::VCODE_IS_WRONG                    => '验证码错误',
        self::ERR_TIMES_GT_THREE                => '错误次数超过3次',
        self::ERR_TIMES_GT_SIX                  => '错误次数超过6次',
        self::UN_NOT_EXIST                      => '该用户名不存在',
        self::PW_IS_WRONG                       => '输入的密码不正确',
        self::VCODE_IS_EMPTY                    => '验证码不能为空',
        self::FREEZED                           => '冻结状态',
        self::SMS_CODE_ERR                      => '短信验证码错误',
        self::LOCKED                            => '锁定状态',
        self::INACTIVE                          => '未激活状态',
        self::PW_NOT_SAME                       => '两次密码不一致',
        self::OUT_OF_NUM                        => '数字超出限定范围',
        self::RELOGIN_ERR                       => '重新登录错误',
        self::VCODE_TIMEOUT                     => '验证码超时',
        self::SMS_CODE_TIMEOUT                  => '短信验证码超时',
        self::SMS_SEND_ERR                      => '发送短信出错',
        self::NOT_TIME                          => '未到时间',
        self::UNDER_CTRL                        => '系统管控中',
        self::UPDATE_ERR                        => '更新数据出错',
        self::INSERT_ERR                        => '写入数据出错',
        self::DEL_ERR                           => '删除数据出错',
        self::READ_ERR                          => '读取数据出错',
        self::ALREADY_IN_ROOM                   => '已经在房间',
        self::UNEXCEPTION                       => '异常访问',
        self::INVALID_REQUEST                   => '请求失效',
        self::LOGIN_FAILED                      => '登录失败'
    );

    // wechat errors
    const INVALID_CREDENTIAL							= 40001;
    const INVALID_GRANT_TYPE							= 40002;
    const INVALID_OPENID								= 40003;
    const INVALID_MEDIA_TYPE							= 40004;
    const INVALID_MEDIA_ID								= 40007;
    const INVALID_MESSAGE_TYPE							= 40008;
    const INVALID_IMAGE_SIZE							= 40009;
    const INVALID_VOICE_SIZE							= 40010;
    const INVALID_VIDEO_SIZE							= 40011;
    const INVALID_THUMB_SIZE							= 40012;
    const INVALID_APPID									= 40013;
    const INVALID_ACCESS_TOKEN							= 40014;
    const INVALID_MENU_TYPE								= 40015;
    const INVALID_BUTTON_SIZE							= 40016;
    const INVALID_BUTTON_TYPE							= 40017;
    const INVALID_BUTTON_NAME_SIZE						= 40018;
    const INVALID_BUTTON_KEY_SIZE						= 40019;
    const INVALID_BUTTON_URL_SIZE						= 40020;
    const INVALID_SUB_BUTTON_SIZE						= 40023;
    const INVALID_SUB_BUTTON_TYPE						= 40024;
    const INVALID_SUB_BUTTON_NAME_SIZE					= 40025;
    const INVALID_SUB_BUTTON_KEY_SIZE					= 40026;
    const INVALID_SUB_BUTTON_URL_SIZE					= 40027;
    const INVALID_CODE									= 40029;
    const INVALID_REFRESH_TOKEN							= 40030;
    const INVALID_TEMPLATE_ID_SIZE						= 40036;
    const INVALID_TEMPLATE_ID 							= 40037;
    const INVALID_URL_SIZE								= 40039;
    const INVALID_URL_DOMAIN							= 40048;
    const INVALID_SUB_BUTTON_URL_DOMAIN					= 40054;
    const INVALID_BUTTON_URL_DOMAIN						= 40055;
    const INVALID_URL 									= 40066;
    const ACCESS_TOKEN_MISSING							= 41001;
    const APPID_MISSING									= 41002;
    const REFRESH_TOKEN_MISSING							= 41003;
    const APPSECRET_MISSING								= 41004;
    const MEDIA_DATA_MISSING							= 41005;
    const MEDIA_ID_MISSING								= 41006;
    const SUB_MENU_DATA_MISSING							= 41007;
    const MISSING_CODE									= 41008;
    const MISSING_OPENID								= 41009;
    const MISSING_URL									= 41010;
    const ACCESS_TOKEN_EXPIRED							= 42001;
    const REFRESH_TOKEN_EXPIRED							= 42002;
    const CODE_EXPIRED									= 42003;
    const REQUIRE_GET_METHOD							= 43001;
    const REQUIRE_POST_METHOD							= 43002;
    const REQUIRE_HTTPS									= 43003;
    const REQUIRE_SUBSCRIBE								= 43004;
    const EMPTY_MEDIA_DATA								= 44001;
    const EMPTY_POST_DATA								= 44002;
    const EMPTY_NEWS_DATA								= 44003;
    const EMPTY_CONTENT									= 44004;
    const EMPTY_LIST_SIZE								= 44005;
    const MEDIA_SIZE_OUT_OF_LIMIT						= 45001;
    const CONTENT_SIZE_OUT_OF_LIMIT						= 45002;
    const TITLE_SIZE_OUT_OF_LIMIT						= 45003;
    const DESCRIPTION_SIZE_OUT_OF_LIMIT					= 45004;
    const URL_SIZE_OUT_OF_LIMIT							= 45005;
    const PICURL_SIZE_OUT_OF_LIMIT						= 45006;
    const PLAYTIME_OUT_OF_LIMIT							= 45007;
    const ARTICLE_SIZE_OUT_OF_LIMIT						= 45008;
    const API_FREQ_OUT_OF_LIMIT							= 45009;
    const CREATE_MENU_LIMIT								= 45010;
    const API_LIMIT 									= 45011;
    const TEMPLATE_SIZE_OUT_OF_LIMIT					= 45012;
    const CANT_MODIFY_SYS_GROUP							= 45016;
    const CANT_SET_GROUP_NAME_TOO_LONG_SYS_GROUP		= 45017;
    const TOO_MANY_GROUP_NOW_NO_NEED_TO_ADD_NEW			= 45018;
    const API_UNAUTHORIZED								= 50001;
}