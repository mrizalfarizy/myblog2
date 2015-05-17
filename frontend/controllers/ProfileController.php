<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Profile;
use frontend\models\search\ProfileSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\PermissionHelpers;
use common\models\RecordHelpers;


/**
 * ProfileController implements the CRUD actions for Profile model.
 */
class ProfileController extends Controller
{
    public function behaviors()
    {
        /*return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];*/
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only'=> ['index','view','create','update','delete'],
                'rules'=> [
                    [
                        'actions' => ['index','view','create','update','delete'],
                        'allow'=> true,
                        'roles'=>['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Profile models.
     * @return mixed
     */
    public function actionIndex()
    {
        /*$searchModel = new ProfileSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);*/
        
        if ($already_exist = RecordHelpers::userHas('profile')){
            return $this->render('view',['model'=>$this->findModel($already_exist),]);
        } else {
            return $this->redirect(['create']);
        }
    }

    /**
     * Displays a single Profile model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        /*return $this->render('view', [
            'model' => $this->findModel($id),
        ]);*/
        
        if ($already_exist = RecordHelpers::userHas('profile')){
            return $this->render('view',['model'=>$this->findModel($already_exist),
            ]);
        } else {
            return $this->redirect(['create']);
        }
    }

    /**
     * Creates a new Profile model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        /*if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }*/
        $model = new Profile();
        $model->user_id= \Yii::$app->user->identity->id;
        
        if ($already_exist = RecordHelpers::userHas('profile')){
            return $this->render('view',['model'=>$this->findModel($already_exist),
            ]);
         } elseif
            ($model->load(Yii::$app->request->post()) && $model->save()){
                return $this->redirect(['view']);
            } else {
                return $this->render('create',['model'=>$model,]);
            }
        
        if ($already_exist = RecordHelpers::userHas('profile')){
            return $this->render ('view',['model'=>$this->findModel($already_exist),]);
        } elseif ($model->load(Yii::$app->request->post())&&$model->save()){
            return $this->redirect(['view']);
        } else {
            return $this->render('create', ['model'=>$model,]);
        }
    }

    /**
     * Updates an existing Profile model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        /*$model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }*/
        if ($model = Profile::find()->where(['user_id'=>Yii::$app->user->identity->id])->one()){
            if ($model->load(Yii::$app->request->post())&&$model->save()){
                return $this->redirect(['view']);
            }else{
                return $this->render('update',['model'=>$model,]);
            }
        }else{
            throw new NotFoundHttpException('no Such profile');
        }
    }

    /**
     * Deletes an existing Profile model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        /*$this->findModel($id)->delete();

        return $this->redirect(['index']);*/
        $model = Profile::find()->where(['user_id'=> Yii::$app->user->id])->one();
        $this->findModel($model->id)->delete();
        return $this->redirect(['site/index']);
    }

    /**
     * Finds the Profile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Profile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Profile::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
