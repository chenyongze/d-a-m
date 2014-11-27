<?php

class CardItemController extends Controller
{

    /**
     * Item列表
     * @author gentle
     */
    public function actionIndex($id)
    {
        set_time_limit(0); //防止执行超时
        //$itemModel = $this->loadModel((int)$id, 'item', 'dataset_id', true);
        $dsModel = $this->loadModel((int)$id, 'ds');
        $dbModel = $this->loadModel((int)$dsModel->database_id, 'db');

        $criteria = new EMongoCriteria();
        $criteria->dataset_id = (int)$id;
        $count = CardItem::model()->count($criteria);
        $pages = new CPagination($count);

        $perPage = 20;
        $pages->pageSize = $perPage;
        //$pages->applyLimit($criteria);
        $offset = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($offset - 1) * $perPage;
        $criteria->limit($perPage)->offset($offset)->sort('id', EMongoCriteria::SORT_DESC);
        $itemModel = CardItem::model()->findAll($criteria);

        $dsModel = $dsModel->sortField();
        $data = array();
        $data['itemModel'] = $itemModel;
        $data['dbModel'] = $dbModel;
        $data['dsModel'] = $dsModel;
        $data['datasetId'] = $dsModel->id;
        $data['dataTree'] = $this->dataTree($dbModel->id);
        $data['info'] = $this->promptInfo();
        $data['pages'] = $pages;
        $this->render('index', $data);
    }

    /**
     * 导入数据
     * @author gentle
     */
    public function actionImport($id)
    {
        set_time_limit(0); //防止执行超时
        $itemModel = new CardItem;
        $dsModel = $this->loadModel($id, 'ds');
        $dbModel = $this->loadModel((int)$dsModel->database_id, 'db');
        $mcss = Yii::app()->mcss;

        if (isset($_FILES['CardItem'])) {
            $file = fopen($_FILES['CardItem']['tmp_name'], 'r');
            $csvHeader = array();
            //往卡牌库Item表导入记录
            //1条1条导入
            for ($i = 0; ; $i++) {
                if ($i < 1) {
                    fgetcsv($file);
                    continue;
                } elseif ($i == 1) {
                    $csvHeader = fgetcsv($file);
                } elseif ($i >= 2) {
                    $itemData = array();
                    $csvData = fgetcsv($file);
                    if (empty($csvData)) {
                        break;
                    }
                    foreach ($csvData as $key => $value) {
                        //只导入元素集有的字段
                        if (isset($dsModel->fields[$csvHeader[$key]])) {
                            $tmpField = $dsModel->fields[$csvHeader[$key]];
                            //$tmpValue = mb_convert_encoding($value, 'UTF-8', 'GBK,GB2312,UTF-8');
                            $tmpValue = $value;

                            if( $tmpField['extra']['field_info']['addition_type'] == "image"){
                                if($tmpValue != '' && is_file($tmpValue)){
                                    $resData = $mcss->uploadImage($tmpValue);
                                    if(is_array($resData)){
                                        $tmpValue = "";
                                    }else{
                                        $tmpValue = $resData;
                                    }
                                }else{
                                    $tmpValue = "";
                                }
                            }
                            elseif (intval($tmpField['extra']['field_info']['field_type']) == 0 && intval($tmpField['extra']['field_info']['addition_type']) == 3) { //单选
                                if (!is_numeric($tmpValue)) {
                                    $tmpValue = array_search($tmpValue, $tmpField['extra']['field_info']['select_value']);
                                    if ($tmpValue === FALSE) {
                                        continue;
                                    }
                                }
                            } else if (intval($tmpField['extra']['field_info']['field_type']) == 0 && intval($tmpField['extra']['field_info']['addition_type']) == 4) { //多选
                                $tmpSelectValue = explode(';', $tmpField['extra']['field_info']['select_value']);
                                $tmpValue = trim($tmpValue, '[]');
                                $tmpMultiArray = array();
                                if (!empty($tmpValue)) {
                                    $tmpValue = explode(',', $tmpValue);
                                    foreach ($tmpValue as $k => $v) {
                                        if (!is_numeric($v)) {
                                            $tmpValueItem = array_search($v, $tmpSelectValue);
                                            //$tmpValueItem = ($tmpValueItem===FALSE) ? '' : $tmpValueItem;
                                            if ($tmpValueItem === FALSE) {
                                                continue;
                                            }
                                            $tmpMultiArray[] = $tmpValueItem;
                                        } else {
                                            $tmpMultiArray[] = $v;
                                        }
                                    }
                                    $tmpValue = $tmpMultiArray;
                                }
                            }
                            $itemData[$csvHeader[$key]] = $tmpValue;
                        }
                    }

                    $itemModel = new CardItem;
                    $saveData['dataset_id'] = (int)$id;
                    $saveData['data'] = $itemData;
                    $itemModel->attributes = $saveData;
                    if (!$itemModel->save()) {
                        Yii::app()->user->setFlash("error", "导入数据失败! 停止行数：" . $i);
                        $this->redirect(array('CardItem/index/id/' . $id));
                    }
                }
            }
            fclose($file);
            Yii::app()->user->setFlash("success", "导入数据成功!");
            $this->redirect(array('CardItem/index/id/' . $id));
        }

        $data = array();
        $data['model'] = $itemModel;
        $data['dsModel'] = $dsModel;
        $data['dbModel'] = $dbModel;
        $data['datasetId'] = $id;
        $this->renderPartial('_form_import', $data);
    }


    /**
     * 发布数据
     * @author gentle
     */
    public function actionCreate($id, $preview = false)
    {
        $dsModel = $this->loadModel($id, 'ds');
        if (isset($_POST['CardItem'])) {
            $itemModel = new CardItem;
            $itemModel->dataset_id = (int)$id;
            $itemModel->attributes = $_POST['CardItem'];
            foreach ($dsModel->fields as $key => $value) {
                if ((isset($value['must']) && $value['must'] == 1) &&
                    (!isset($itemModel->attributes['data'][$key]) ||
                        (is_string($itemModel->attributes['data'][$key]) && trim($itemModel->attributes['data'][$key]) == '') ||
                        (is_array($itemModel->attributes['data'][$key]) && empty($itemModel->attributes['data'][$key])))
                ) {
                    Yii::app()->user->setFlash("error", "必填项 {$value['name']} 无数据!");
                    $this->redirect(array('CardItem/index/id/' . $id));
                }
            }

            if ($itemModel->save()) {
                Yii::app()->user->setFlash("success", "发布数据成功!");
            } else {
                Yii::app()->user->setFlash("error", "发布数据失败!");
            }
            $this->redirect(array('CardItem/index/id/' . $id));
        }

        //构造字段Html
        $dsModel = $dsModel->sortField();
        $fieldHtml = '';
        foreach ($dsModel->fields as $key => $value) {
            if ($value['type'] == 'field') {
                $fieldHtml .= $this->fieldItemHtml($key, $value);
            } elseif ($value['type'] == 'group') {
                $groupData = array();
                $groupData['datasetId'] = $id;
                $groupData['enName'] = $key;
                $groupData['data'] = $value;
                $groupData['html'] = $this->groupItemHtml($id, $key);

                $fieldHtml .= $this->renderPartial('_form_item_group', $groupData, true);
            }
        }

        $data = array();
        $data['model'] = $dsModel;
        $data['datasetId'] = $id;
        $data['fieldHtml'] = $fieldHtml;
        $data['preview'] = $preview;
        //$this->renderPartial('_form_item', $data);
        $this->render('_form_item', $data);
    }

    public function actionGroupItemHtml($id, $group, $index = '[key]', $itemData = array())
    {
        return $this->groupItemHtml($id, $group, $index, $itemData, true);
    }

    public function groupItemHtml($id, $group, $index = '[key]', $itemData = array(), $output = false)
    {
        $dsModel = $this->loadModel($id, 'ds');
        $dsModel = $dsModel->sortField();
        $groupInfo = $dsModel->fields[$group];

        $html = '';
        if (!empty($groupInfo['fields'])) {
            foreach ($groupInfo['fields'] as $key => $value) {
                $html .= $this->fieldItemHtml($key, $value, $group, $index, $itemData);
            }
        }
        if ($output) {
            echo $html;
        } else {
            return $html;
        }
    }

    private function fieldItemHtml($key, $value, $group = '', $index = 'key', $itemData = '')
    {
        $fieldData = array();
        $fieldData['enName'] = $key;
        $fieldData['data'] = $value;
        if ($group) {
            $fieldData['group'] = $group;
            $fieldData['key'] = $index;
        }
        if ($itemData) {
            $fieldData['itemData'] = $itemData;
        }

        if ($value['extra']['field_info']['field_type'] == 'reference') {
            $fieldType = $value['extra']['field_info']['field_type'];
            $additionType = $value['extra']['field_info']['addition_type'];
            $dataset = CardDs::model()->findByAttributes(array('en_name' => $additionType));
            $dsId = $dataset['id'];
            $fieldData['referenceItems'] = $this->loadModel($dsId, 'item', 'dataset_id', true);
            $tplName = '_form_item_' . $fieldType;
        } else {
            $fieldType = $value['extra']['field_info']['field_type'];
            $additionType = $value['extra']['field_info']['addition_type'];
            $tplName = '_form_item_' . $fieldType . '_' . $additionType;
        }

        $html = $this->renderPartial($tplName, $fieldData, true);
        return $html;
    }

    /**
     * 修改数据
     * @author gentle
     */
    public function actionUpdate($id)
    {

        $itemModel = $this->loadModel($id, 'item');
        $dsId = (int)$itemModel->dataset_id;
        $dsModel = $this->loadModel($dsId, 'ds');
        if (isset($_POST['CardItem'])) {
            $itemModel->attributes = $_POST['CardItem'];
            if ($itemModel->save()) {
                Yii::app()->user->setFlash("success", "修改数据成功!");
                $this->redirect(array('CardItem/index/id/' . $dsId));
            } else {
                Yii::app()->user->setFlash("error", "修改数据失败!");
            }
        }

        //构造字段Html
        $dsModel = $dsModel->sortField();
        $fieldHtml = '';
        foreach ($dsModel->fields as $key => $value) {
            if ($value['type'] == 'field') {
                $fieldType = $value['extra']['field_info']['field_type'];
                $fieldHtml .= $this->fieldItemHtml($key, $value, '', '', $itemModel->data);
            } elseif ($value['type'] == 'group') {
                $groupData = array();
                $groupData['datasetId'] = $dsId;
                $groupData['enName'] = $key;
                $groupData['data'] = $value;
                if (!empty($itemModel->data[$key])) {
                    $groupData['dataHtml'] = array();
                    foreach ($itemModel->data[$key] as $k => $v) {
                        $groupData['dataHtml'][] = $this->groupItemHtml($dsId, $key, $k, $v);
                    }
                }
                $groupData['html'] = $this->groupItemHtml($dsId, $key);

                $fieldHtml .= $this->renderPartial('_form_item_group', $groupData, true);
            }
        }

        $data = array();
        $data['model'] = $itemModel;
        $data['datasetId'] = $dsId;
        $data['fieldHtml'] = $fieldHtml;
        $data['update'] = true;
        $data['preview'] = false;
        $this->render('_form_item', $data);
    }

    /**
     * 删除内容
     * @param integer $id 数据id
     * @author gentle
     */
    public function actionDelete()
    {

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $model = $this->loadModel($id, 'item');
            if ($model->delete()) {
                Yii::app()->user->setFlash("success", "删除数据成功!");
            } else {
                Yii::app()->user->setFlash("error", "删除数据失败!");
            }
        } else {
            $ids = $_POST['CardItem']['id'];
            foreach ($ids as $value) {
                $model = $this->loadModel($value, 'item');
                if (!$model->delete()) {
                    Yii::app()->user->setFlash("error", "删除数据失败!");
                }
            }
            Yii::app()->user->setFlash("success", "删除数据成功!");
        }
        $this->redirect(array('CardItem/index/id/' . $model->dataset_id));
    }

    public function actionUploadImage($name)
    {
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $url = Yii::app()->mcss->uploadImage($_FILES['image']['tmp_name']);
            if ($url) {
                RestHelper::success(array('url' => $url));
            } else {
                RestHelper::error('图片上传到云存储失败');
            }
        } else {
            RestHelper::error('图片上传失败');
        }
    }

}
