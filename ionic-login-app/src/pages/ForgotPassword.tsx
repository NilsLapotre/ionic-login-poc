import { IonContent,IonRow, IonGrid, IonHeader, IonPage, IonTitle, IonToolbar, IonInput, IonItem, IonCol, IonLabel, IonAlert, IonButton } from '@ionic/react';
import React, { useState } from 'react';


const ForgotPassword: React.FC = () => {
  const [email, setEmail] = useState<string>();
  const [iserror, setIserror] = useState<boolean>(false);
  const [message, setMessage] = useState<string>("");
 
  const validateEmail = () => {
    var regexp = new RegExp('.+\@.+\..+');
    if (regexp.test(email!)) {
      return true;
    }
    return false;
  }

  const handleSubmit = () => {
    if (!email) {
        setMessage("Please enter a valid email");
        setIserror(true);
        return;
    }
    if (validateEmail() === false) {
        setMessage("Your email is invalid");
        setIserror(true);
        return;
    }

   //Appel vers service
  };


  return (
    <IonPage>
    <IonHeader>
      <IonToolbar>
        <IonTitle>Forgot password</IonTitle>
      </IonToolbar>
    </IonHeader>
    <IonContent fullscreen className="ion-padding ion-text-center">
      <IonGrid>
      <IonRow>
        <IonCol>
          <IonAlert
              isOpen={iserror}
              onDidDismiss={() => setIserror(false)}
              cssClass="my-custom-class"
              header={"Error!"}
              message={message}
              buttons={["Dismiss"]}
          />
        </IonCol>
      </IonRow>
      <IonRow>
          <IonCol>
            <IonLabel position="floating"> Enter your email to reset your password</IonLabel>    
          </IonCol>
        </IonRow>
        <IonRow>
          <IonCol>
          <IonItem>
          <IonLabel position="floating"> Email</IonLabel>
          <IonInput
              type="email"
              value={email}
              onIonChange={(e)=> { setEmail(e.detail.value!)}}
              >
          </IonInput>
          </IonItem>
          </IonCol>
        </IonRow>
        <IonRow>
          <IonCol>
            <IonButton expand="block" onClick={handleSubmit}>Envoyer</IonButton>
           </IonCol>
        </IonRow>
      </IonGrid>
    </IonContent>
  </IonPage>
);
};
export default ForgotPassword;
