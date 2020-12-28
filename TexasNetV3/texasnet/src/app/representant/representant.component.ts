import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ModuleService } from '../services/modules.service';

@Component({
  selector: 'app-representant',
  templateUrl: './representant.component.html',
  styleUrls: ['./representant.component.css']
})
export class RepresentantComponent implements OnInit {
  mode:string=''; // Défini l'affichage du menu representant
  maintenance:boolean;
  constructor(private router:Router, private moduleService : ModuleService) { }

  ngOnInit() {
    this.moduleService.infoModules().then(data=>{
      if(data["maintenance"]==1){
        this.maintenance = (true);
      } else {
        this.maintenance = (false);
      }
    });


    if(sessionStorage.getItem("representant")!=='true'){
      this.router.navigate(['']);
      sessionStorage.clear();
    }
    this.mode = 'liste';
  }

  changeMode(nouveauMode) {
    this.mode = nouveauMode;
  }
}
