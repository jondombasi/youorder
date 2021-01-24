var db = openDatabase('db5', '1.0', 'Test DB', 5*1024*1024);

$(document).ready(function(){
  db.transaction(function (tx) {
    //tx.executeSql('DROP TABLE localstorage');
    tx.executeSql('CREATE TABLE IF NOT EXISTS commandes (id INTEGER PRIMARY KEY, restaurant, client, commentaire, date_debut, date_fin, date_ajout, statut, raison_refus, comm_refus, date_statut, distance, duree, livreur, signature)');
    tx.executeSql('CREATE TABLE IF NOT EXISTS restaurant (id INTEGER PRIMARY KEY, nom, adresse, latitude, longitude, numero)');
    tx.executeSql('CREATE TABLE IF NOT EXISTS client (id INTEGER PRIMARY KEY, nom, adresse, latitude, longitude, numero)');
    tx.executeSql('CREATE TABLE IF NOT EXISTS livreur_planning (id INTEGER PRIMARY KEY, id_livreur, id_commercant, id_vehicule, date_debut, date_fin)');
    tx.executeSql('CREATE TABLE IF NOT EXISTS localstorage (id INTEGER PRIMARY KEY, id_livreur, email, password, id_vehicule, id_commande, id_commercant, id_connexion)');
    tx.executeSql('INSERT INTO localstorage (id, id_livreur, email, password, id_vehicule, id_commande, id_commercant, id_connexion) VALUES (1, 0, "", "", 0, 0, 0, 0)');
  });
});

function change_statut(page, statut, id_commande, id_livreur, raison_refus, comm_refus, event) {
  switch(statut) {
    case "ajouté":
      new_statut="réservé";
      console.log("le livreur s'assigne la commande d'id "+id_commande)
      break;
    case "réservé":
      new_statut="récupéré";
      console.log("le livreur récupère la commande d'id "+id_commande)
      break;
    case "récupéré":
      new_statut="";
      console.log("le livreur a terminé la commande d'id "+id_commande)
      db.transaction(function (tx) {
          tx.executeSql(
            'UPDATE localstorage SET id_commande=? WHERE id=1', 
            [id_commande], 
            function (tx, results) {
              window.location.href='finalisation.html';
            },
            function (err) {
              console.log(err);
            }
          );
      });
      break;
    case "echec":
      new_statut="echec"; 
      console.log("la commande d'id "+id_commande+" n'a pas été livrée");
  }

  if (new_statut!="") {
    $.ajax({
      url      : 'http://www.you-order.eu/webservices/action_webservice.php',
      data     : 'action=change_statut&id_livreur='+id_livreur+'&id_commande='+id_commande+'&statut='+new_statut+'&raison_refus='+raison_refus+'&comm_refus='+comm_refus,
      type     : "GET",
      cache    : false,
      timeout: 2000,
      success: function(transport) {
        console.log(transport);
        console.log("ok");

        new_date=new Date();
        new_date=new_date.getFullYear()+"-"+('0'+(new_date.getMonth()+1)).slice(-2)+"-"+("0"+new_date.getDate()).slice(-2)+" "+("0"+new_date.getHours()).slice(-2)+":"+("0"+new_date.getMinutes()).slice(-2)+":"+("0"+new_date.getSeconds()).slice(-2);

        db.transaction(function (tx) {
          tx.executeSql(
            'UPDATE commandes SET statut=?, livreur=?, date_statut=?, raison_refus=?, comm_refus=? WHERE id=?', 
            [new_statut, id_livreur, new_date, raison_refus, comm_refus, id_commande], 
            function (tx, results) {
              if (page=="commandes") {
                load_commandes();
                setMarker();
                if (event!="") {
                  setTimeout(function(){event.css('display', 'none');}, 300); 
                }
              }
              else {
                window.location.href='commandes.html';
              }
            },
            function (err) {
              console.log(err);
            }
          );
        });
      },
      error: function(transport) {
        console.log(transport);
        console.log("erreur")
        if (new_statut!="echec" && new_statut!="signé") {
          popup.style.display = "block";
        }
      }
    });
  }
}

String.prototype.toHHMMSS = function () {
  var sec_num = parseInt(this, 10); // don't forget the second param
  var hours   = Math.floor(sec_num / 3600);
  var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
  var seconds = sec_num - (hours * 3600) - (minutes * 60);

  if (hours   < 10) {hours   = "0"+hours;}
  if (minutes < 10) {minutes = "0"+minutes;}
  if (seconds < 10) {seconds = "0"+seconds;}

  if (hours=="00") {
    duree_aff=minutes+"min";
  }
  else {
    duree_aff=hours+"h"+minutes
  }

  return duree_aff;
}

function getFullDate(dateString) {
  ts_date=new Date(dateString);
  day_name=['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'][ts_date.getDay()];
  day_number=('0'+(ts_date.getDate())).slice(-2);
  month_name=['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'][ts_date.getMonth()];
  year=ts_date.getFullYear();

  return day_name+" "+day_number+" "+month_name+" "+year;
}

function deconnexion() {
  db.transaction(function (tx) {
    tx.executeSql(
      'UPDATE localstorage SET id_livreur=?, email=?, password=? WHERE id=?', 
      [0, "", "", 1], 
      function (tx, results) {
        window.location = "index.html";
      },
      function (err) {
        console.log(err);
      }
    );
  });
}