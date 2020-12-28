import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ContenuMaintenanceComponent } from './contenu-maintenance.component';

describe('ContenuMaintenanceComponent', () => {
  let component: ContenuMaintenanceComponent;
  let fixture: ComponentFixture<ContenuMaintenanceComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ContenuMaintenanceComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ContenuMaintenanceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
