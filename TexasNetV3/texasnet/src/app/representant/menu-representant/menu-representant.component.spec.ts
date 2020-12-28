import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MenuRepresentantComponent } from './menu-representant.component';

describe('MenuRepresentantComponent', () => {
  let component: MenuRepresentantComponent;
  let fixture: ComponentFixture<MenuRepresentantComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MenuRepresentantComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MenuRepresentantComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
