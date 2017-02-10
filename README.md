# 简介

采用离线数据库的hitokoto for WordPress。来源请见Fork源。

# 使用说明

安装并启用插件后，在模板调用以下函数。

```php
/**
 * 打印/返回一条一言的某个属性
 * 不会自动刷新，请调用hitokoto_read()另读一条
 * 属性包括：id hitokoto cat catname author source date
 */
hitikoto($type, $print = true);

/**
 * 刷新hitikoto()所缓存的一言
 */
hitokoto_read();

/**
 * 随机打印一条一言的正文
 */
hitokoto_single();

/**
 * 随机返回一条一言的数组
 */
hitokoto_full()
```

# 在线更新本地源

```bash
$ bash update.sh
```

# 其他说明

[在线源](https://kotori.sinaapp.com/hitokoto/json) 从[主站](http://hitokoto.us)同步，目前共有493条记录。

更多信息请参见原站和Fork源。